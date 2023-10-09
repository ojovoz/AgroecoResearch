package ojovoz.agroecoresearch;

import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.support.design.widget.FloatingActionButton;
import android.support.v7.app.AppCompatActivity;
import android.util.TypedValue;
import android.view.Gravity;
import android.view.View;
import android.widget.Button;
import android.widget.LinearLayout;

/**
 * Created by Eugenio on 14/03/2017.
 */
public class mainMenu extends AppCompatActivity {

    public int userId;
    public int userRole;

    private preferenceManager prefs;
    boolean bCatalogsDownloaded=false;

    private notificationHelper notifications;

    @Override
    public void onCreate(Bundle savedInstanceState) {

        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main_menu);
        userId = getIntent().getExtras().getInt("userId");
        userRole = getIntent().getExtras().getInt("userRole");

        checkCatalogsDownloaded();

        notifications = new notificationHelper(this);
        createButtons();
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
        logout();
    }

    public void createButtons(){

        Button b;

        if(!bCatalogsDownloaded){
            b = (Button)findViewById(R.id.registerInput);
            b.setVisibility(View.GONE);

            b = (Button)findViewById(R.id.registerActivity);
            b.setVisibility(View.GONE);

            b = (Button)findViewById(R.id.registerMeasurement);
            b.setVisibility(View.GONE);

            b = (Button)findViewById(R.id.manageData);
            b.setVisibility(View.GONE);
        }

        if(!notifications.notificationsPending(userId)){
            FloatingActionButton fb = (FloatingActionButton)findViewById(R.id.notificationsButton);
            fb.setVisibility(View.GONE);
        }

    }

    public void checkCatalogsDownloaded(){

        prefs = new preferenceManager(this);

        bCatalogsDownloaded = (prefs.exists("crops") && prefs.exists("treatments") && prefs.exists("measurements") && prefs.exists("activities") && prefs.exists("fields"));
    }

    public void addActivity(View v){
        final Context context = this;
        Intent i = new Intent(context, chooseFieldPlot.class);
        i.putExtra("userId", userId);
        i.putExtra("userRole", userRole);
        i.putExtra("task", "activity");
        i.putExtra("field", -1);
        i.putExtra("activity", -1);
        i.putExtra("title","");

        startActivity(i);
        finish();
    }

    public void addInput(View v){
        final Context context = this;
        Intent i = new Intent(context, inputChooser.class);
        i.putExtra("userId",userId);
        i.putExtra("userRole",userRole);
        i.putExtra("task","input");
        i.putExtra("field",-1);
        startActivity(i);
        finish();
    }

    public void addMeasurement(View v){
        final Context context = this;
        Intent i = new Intent(context, measurementChooser.class);
        i.putExtra("userId",userId);
        i.putExtra("userRole",userRole);
        i.putExtra("task","measurement");
        startActivity(i);
        finish();
    }

    public void manageData(View v){
        final Context context = this;
        Intent i = new Intent(context, manageData.class);
        i.putExtra("userId",userId);
        i.putExtra("userRole",userRole);
        i.putExtra("update","");
        startActivity(i);
        finish();
    }

    public void settings(View v){
        final Context context = this;
        Intent i = new Intent(context, settings.class);
        i.putExtra("userId",userId);
        i.putExtra("userRole",userRole);
        startActivity(i);
        finish();
    }

    public void logout(){
        AlertDialog.Builder logoutDialog = new AlertDialog.Builder(this);
        logoutDialog.setTitle(R.string.logoutAlertTitle);
        logoutDialog.setMessage(R.string.logoutAlertString);
        logoutDialog.setNegativeButton(R.string.cancelButtonText,null);
        logoutDialog.setPositiveButton(R.string.okButtonText, new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialogInterface, int i) {
                doLogout(null);
            }
        });
        logoutDialog.create();
        logoutDialog.show();

    }

    public void doLogout(View v){
        final Context context = this;
        Intent i = new Intent(context, loginScreen.class);
        startActivity(i);
        finish();
    }

    public void notifications(View v){
        final Context context = this;
        Intent i = new Intent(context, notifications.class);
        i.putExtra("userId",userId);
        i.putExtra("userRole",userRole);
        startActivity(i);
        finish();
    }
}
