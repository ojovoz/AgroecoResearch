package ojovoz.agroecoresearch;

import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.support.v7.app.AppCompatActivity;
import android.view.View;
import android.widget.CheckBox;
import android.widget.Toast;

import java.io.File;
import java.io.FileWriter;
import java.io.IOException;
import java.io.StringReader;
import java.util.ArrayList;


import au.com.bytecode.opencsv.CSVReader;
import au.com.bytecode.opencsv.CSVWriter;

/**
 * Created by Eugenio on 29/03/2017.
 */
public class downloadCatalogs extends AppCompatActivity implements httpConnection.AsyncResponse {

    public int userId;
    public int userRole;
    public String server;

    private preferenceManager prefs;
    private boolean bConnecting = false;

    private ArrayList<String> download;
    private int index;
    private ProgressDialog dialog;
    private int uploadIncrement = 1;

    @Override
    public void onCreate(Bundle savedInstanceState) {

        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_download_catalogs);
        userId = getIntent().getExtras().getInt("userId");
        userRole = getIntent().getExtras().getInt("userRole");
        server = getIntent().getExtras().getString("server");

        initializeChecboxes();
    }

    /*
    @Override public void onResume() {
        super.onResume();
        if(userId==0){
            final Context context = this;
            Intent i;
            i = new Intent(context, loginScreen.class);
            startActivity(i);
            finish();
            return;
        }
    }
    */

    @Override public void onBackPressed(){
        settings();
    }

    public void settings(){
        final Context context = this;
        Intent i = new Intent(context, settings.class);
        i.putExtra("userId",userId);
        i.putExtra("userRole",userRole);
        startActivity(i);
        finish();
    }

    private void initializeChecboxes(){
        prefs = new preferenceManager(this);

        CheckBox crops = (CheckBox)findViewById(R.id.downloadCrops);
        crops.setChecked(!prefs.exists("crops"));
        CheckBox treatments = (CheckBox)findViewById(R.id.downloadTreatments);
        treatments.setChecked(!prefs.exists("treatments"));
        CheckBox measurements = (CheckBox)findViewById(R.id.downloadMeasurements);
        measurements.setChecked(!prefs.exists("measurements"));
        CheckBox activities = (CheckBox)findViewById(R.id.downloadActivities);
        activities.setChecked(!prefs.exists("activities"));
        CheckBox fields = (CheckBox)findViewById(R.id.downloadFields);
        fields.setChecked(!prefs.exists("fields"));
    }

    public void downloadSelected(View v){
        if(!bConnecting) {
            download = new ArrayList();
            CheckBox crops = (CheckBox) findViewById(R.id.downloadCrops);
            if (crops.isChecked()) {
                download.add("crops");
            }
            CheckBox treatments = (CheckBox) findViewById(R.id.downloadTreatments);
            if (treatments.isChecked()) {
                download.add("treatments");
                download.add("treatment_colors");
            }
            CheckBox measurements = (CheckBox) findViewById(R.id.downloadMeasurements);
            if (measurements.isChecked()) {
                download.add("measurements");
                download.add("measurements_applied");
                download.add("health_report_items");
            }
            CheckBox activities = (CheckBox) findViewById(R.id.downloadActivities);
            if (activities.isChecked()) {
                download.add("activities");
                download.add("activities_applied");
            }
            CheckBox fields = (CheckBox) findViewById(R.id.downloadFields);
            if (fields.isChecked()) {
                download.add("fields");
            }

            if (download.size() > 0) {
                httpConnection http = new httpConnection(this,this);
                if(http.isOnline()) {
                    index = 0;
                    CharSequence dialogTitle = getString(R.string.downloadCatalogsProgressDialogTitle) + " " + download.get(index);

                    dialog = new ProgressDialog(this);
                    dialog.setCancelable(true);
                    dialog.setCanceledOnTouchOutside(false);
                    dialog.setMessage(dialogTitle);
                    dialog.setProgressStyle(ProgressDialog.STYLE_HORIZONTAL);
                    dialog.setProgress(0);
                    dialog.setMax(download.size() - 1);
                    dialog.show();
                    dialog.setOnCancelListener(new DialogInterface.OnCancelListener() {
                        @Override
                        public void onCancel(DialogInterface d) {
                            bConnecting = false;
                            finishActivity();
                        }
                    });
                    doDownload();
                } else {
                    Toast.makeText(this, R.string.pleaseConnectMessage, Toast.LENGTH_SHORT).show();
                    bConnecting=false;
                }
            } else {
                Toast.makeText(this, R.string.noCatalogsSelectedText, Toast.LENGTH_SHORT).show();
            }
        }
    }

    private void doDownload(){
        httpConnection http = new httpConnection(this,this);
        if (http.isOnline()){
            if (!bConnecting) {
                bConnecting = true;
                http.execute(server + "/mobile/get_" + download.get(index) + ".php?user_id=" + userId,"csv");
            }
        } else {
            Toast.makeText(this, R.string.pleaseConnectMessage, Toast.LENGTH_SHORT).show();
            bConnecting=false;
        }
    }

    @Override
    public void processFinish(String output){
        bConnecting=false;
        String[] nextLine;
        CSVReader reader = new CSVReader(new StringReader(output),',','"');
        deleteCatalog(download.get(index));
        File file = new File(this.getFilesDir(), download.get(index));
        try {
            FileWriter w = new FileWriter(file);
            CSVWriter writer = new CSVWriter(w, ',', '"');
            while((nextLine = reader.readNext()) != null){
                writer.writeNext(nextLine);
            }
            writer.close();
            reader.close();
            prefs.savePreferenceBoolean(download.get(index),true);
        } catch (IOException e) {

        }
        index++;
        if(index<download.size()){
            progressHandler.sendMessage(progressHandler.obtainMessage());
            doDownload();
        } else {
            bConnecting=false;
            dialog.dismiss();
            finishActivity();
        }
    }

    Handler progressHandler = new Handler() {
        @Override
        public void handleMessage(Message msg) {
            dialog.incrementProgressBy(uploadIncrement);
            CharSequence dialogTitle=getString(R.string.downloadCatalogsProgressDialogTitle) + " " + download.get(index);
            dialog.setMessage(dialogTitle);
            if (dialog.getProgress() == dialog.getMax()) {
                bConnecting=false;
                dialog.dismiss();
                finishActivity();
            }
        }
    };

    private void finishActivity() {
        final Context context = this;
        Intent i = new Intent(context, settings.class);
        i.putExtra("userId",userId);
        i.putExtra("userRole",userRole);
        i.putExtra("server",server);
        startActivity(i);
        this.finish();
    }

    private void deleteCatalog(String filename){
        this.deleteFile(filename);
    }
}
