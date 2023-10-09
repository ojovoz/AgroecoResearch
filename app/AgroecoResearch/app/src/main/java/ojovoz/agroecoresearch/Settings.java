package ojovoz.agroecoresearch;

import android.content.Context;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.view.View;
import android.widget.Button;
import android.widget.Toast;

/**
 * Created by Eugenio on 15/03/2017.
 */
public class settings extends AppCompatActivity implements httpConnection.AsyncResponse {

    public int userId;
    public int userRole;
    public String server = "";

    private promptDialog dlg = null;
    private preferenceManager prefs;

    @Override
    public void onCreate(Bundle savedInstanceState) {

        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_settings);
        userId = getIntent().getExtras().getInt("userId");
        userRole = getIntent().getExtras().getInt("userRole");

        if(userRole==0){
            Button b1 = (Button)findViewById(R.id.defineServerButton);
            b1.setVisibility(View.GONE);

            Button b2 = (Button)findViewById(R.id.controlPanelButton);
            b2.setVisibility(View.GONE);
        }

        initializeVariables();
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

    @Override
    public void onBackPressed(){
        final Context context = this;
        Intent i = new Intent(context, mainMenu.class);
        i.putExtra("userId",userId);
        i.putExtra("userRole",userRole);
        startActivity(i);
        finish();
    }

    private void initializeVariables(){
        prefs = new preferenceManager(this);
        server = prefs.getPreference("server");
        if (server.equals("")) {
            defineServer("");
        }
    }

    public void defineServerButton(View v){
        defineServer(server);
    }

    public void defineServer(String current) {
        dlg = new promptDialog(this, R.string.emptyString, R.string.defineServerLabel, current) {
            @Override
            public boolean onOkClicked(String input) {
                if(!input.startsWith("http://")){
                    input="http://"+input;
                }
                settings.this.server = input;
                prefs.savePreference("server", input);
                prefs.savePreference("users","");
                prefs.savePreference("mail","");
                prefs.savePreference("password","");
                prefs.savePreference("smtpServer","");
                prefs.savePreference("smtpPort","");
                return true;
            }
        };
        dlg.show();
    }

    public void controlPanel(View v){
        httpConnection http = new httpConnection(this,this);
        if (http.isOnline()) {
            String url = server + "/control/";
            Intent i = new Intent(Intent.ACTION_VIEW);
            if(i.resolveActivity(getPackageManager())!=null) {
                i.setData(Uri.parse(url));
                startActivity(i);
            }
        } else {
            Toast.makeText(this, R.string.pleaseConnectMessage, Toast.LENGTH_SHORT).show();
        }

    }

    public void downloadCatalogs(View v){
        final Context context = this;
        Intent i = new Intent(context, downloadCatalogs.class);
        i.putExtra("userId",userId);
        i.putExtra("userRole",userRole);
        i.putExtra("server",server);
        startActivity(i);
        finish();
    }

    @Override
    public void processFinish(String output){

    }
}
