package ojovoz.agroecoresearch;

import android.app.AlertDialog;
import android.app.Dialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.MenuItem;
import android.view.View;
import android.view.Window;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.ListAdapter;
import android.widget.TextView;
import android.widget.Toast;

import java.lang.reflect.Array;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.Iterator;
import java.util.TimeZone;

/**
 * Created by Eugenio on 06/04/2017.
 */
public class enterActivity extends AppCompatActivity {

    public int userId;
    public int userRole;
    public String task;
    public int logId;
    public int fieldId;
    public String plots;
    public int activityId;
    public String activityTitle;
    public String shortTitle;
    public String activityMeasurementUnits;

    public Date activityDate;

    public String update;

    public boolean changes=false;
    public int exitAction;

    float valueNumber;
    String unitsText;
    String laborersNumber;
    String costValue;
    String commentsText;

    agroecoHelper agrohelper;
    ArrayList<oActivity> activities;
    CharSequence activitiesArray[];
    String activityName;

    @Override
    public void onCreate(Bundle savedInstanceState) {

        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_enter_activity);

        userId = getIntent().getExtras().getInt("userId");
        userRole = getIntent().getExtras().getInt("userRole");
        task = getIntent().getExtras().getString("task");
        fieldId = getIntent().getExtras().getInt("field");
        plots = getIntent().getExtras().getString("plots");
        activityId = getIntent().getExtras().getInt("activity");
        activityTitle = getIntent().getExtras().getString("title");
        shortTitle = getIntent().getExtras().getString("shortTitle");
        activityMeasurementUnits = getIntent().getExtras().getString("units");
        update = getIntent().getExtras().getString("update");

        TextView tt = (TextView)findViewById(R.id.fieldPlotText);
        tt.setText(activityTitle);

        agrohelper = new agroecoHelper(this,"fields,crops,treatments,activities");
        activities = agrohelper.getActivitiesForPlots(fieldId,plots);
        ArrayList<String> activityNames = new ArrayList<>();
        Iterator<oActivity> iterator = activities.iterator();
        while (iterator.hasNext()) {
            oActivity a = iterator.next();
            activityNames.add(a.activityName);
        }
        activitiesArray = activityNames.toArray(new CharSequence[activityNames.size()]);

        EditText et = (EditText)findViewById(R.id.activityUnits);
        et.setText(activityMeasurementUnits);
        EditText av = (EditText)findViewById(R.id.activityValue);
        EditText al = (EditText)findViewById(R.id.activityLaborers);
        EditText ak = (EditText)findViewById(R.id.activityCost);
        EditText ac = (EditText)findViewById(R.id.activityComments);

        if(update.equals("activity")){
            logId = getIntent().getExtras().getInt("logId");

            Button ob = (Button)findViewById(R.id.okButton);
            ob.setText(R.string.editButtonText);

            Button db = (Button)findViewById(R.id.dateButton);
            db.setText(getIntent().getExtras().getString("date"));
            activityDate = stringToDate(getIntent().getExtras().getString("date"));

            Button ab = (Button)findViewById(R.id.activityButton);
            ab.setText(agrohelper.getActivityNameFromId(activityId));

            av.setText(Float.toString(getIntent().getExtras().getFloat("activityValue")));
            al.setText(getIntent().getExtras().getString("activityLaborers"));
            ak.setText(getIntent().getExtras().getString("activityCost"));
            ac.setText(getIntent().getExtras().getString("activityComments"));
        } else {
            activityDate = new Date();

            Button ab = (Button)findViewById(R.id.activityButton);
            ab.setText(R.string.chooseActivityButtonTitle);
        }

        et.addTextChangedListener(new TextWatcher() {
            @Override
            public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) {

            }

            @Override
            public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) {
                changes=true;
            }

            @Override
            public void afterTextChanged(Editable editable) {

            }
        });

        av.addTextChangedListener(new TextWatcher() {
            @Override
            public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) {

            }

            @Override
            public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) {
                changes=true;
            }

            @Override
            public void afterTextChanged(Editable editable) {

            }
        });

        al.addTextChangedListener(new TextWatcher() {
            @Override
            public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) {

            }

            @Override
            public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) {
                changes=true;
            }

            @Override
            public void afterTextChanged(Editable editable) {

            }
        });

        ak.addTextChangedListener(new TextWatcher() {
            @Override
            public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) {

            }

            @Override
            public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) {
                changes=true;
            }

            @Override
            public void afterTextChanged(Editable editable) {

            }
        });

        ac.addTextChangedListener(new TextWatcher() {
            @Override
            public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) {

            }

            @Override
            public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) {
                changes=true;
            }

            @Override
            public void afterTextChanged(Editable editable) {

            }
        });

        Button cb = (Button)findViewById(R.id.dateButton);
        cb.setText(dateToString(activityDate));
        cb.setOnClickListener(new View.OnClickListener(){
            @Override
            public void onClick(View v){
                displayDatePicker();
            }
        });
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
        if(changes) {
            exitAction = 0;
            confirmExit();
        } else {
            goBack();
        }
    }

    public void confirmExit(){
        AlertDialog.Builder logoutDialog = new AlertDialog.Builder(this);
        logoutDialog.setTitle(R.string.exitAlertTitle);
        logoutDialog.setMessage(R.string.exitAlertString);
        logoutDialog.setNegativeButton(R.string.cancelButtonText,null);
        logoutDialog.setPositiveButton(R.string.okButtonText, new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialogInterface, int i) {
                switch (exitAction){
                    case 0:
                        goBack();
                        break;
                    case 1:
                        goToDataManager();
                        break;
                    case 2:
                        goToMainMenu();
                        break;
                }
            }
        });
        logoutDialog.create();
        logoutDialog.show();
    }

    public void goBack(){
        final Context context = this;
        if(update.equals("")) {
            Intent i = new Intent(context, chooseFieldPlot.class);
            i.putExtra("userId", userId);
            i.putExtra("userRole", userRole);
            i.putExtra("task", task);
            i.putExtra("field", fieldId);
            i.putExtra("plots", plots);
            i.putExtra("newActivity", false);
            i.putExtra("activity",-1);
            i.putExtra("title",shortTitle);
            startActivity(i);
            finish();
        } else {
            Intent i = new Intent(context, manageData.class);
            i.putExtra("userId", userId);
            i.putExtra("userRole", userRole);
            i.putExtra("update","");
            startActivity(i);
            finish();
        }
    }

    @Override
    public boolean onCreateOptionsMenu(android.view.Menu menu) {
        super.onCreateOptionsMenu(menu);
        menu.add(0, 0, 0, R.string.opManageData);
        menu.add(1, 1, 1, R.string.opMainMenu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        switch (item.getItemId()) {
            case 0:
                if(changes) {
                    exitAction = 1;
                    confirmExit();
                } else {
                    goToDataManager();
                }
                break;
            case 1:
                if(changes) {
                    exitAction = 2;
                    confirmExit();
                } else {
                    goToMainMenu();
                }
        }
        return super.onOptionsItemSelected(item);
    }

    public void goToDataManager(){
        final Context context = this;
        Intent i = new Intent(context, manageData.class);
        i.putExtra("userId",userId);
        i.putExtra("userRole",userRole);
        i.putExtra("update","");
        startActivity(i);
        finish();
    }

    public void goToMainMenu(){
        final Context context = this;
        Intent i = new Intent(context, mainMenu.class);
        i.putExtra("userId",userId);
        i.putExtra("userRole",userRole);
        startActivity(i);
        finish();
    }

    public void showActivities(View v){
        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        builder.setCancelable(true);
        builder.setNegativeButton(R.string.cancelButtonText, new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int which) {
                dialog.dismiss();
            }
        });
        final ListAdapter adapter = new ArrayAdapter<>(this, R.layout.checked_list_template, activitiesArray);
        builder.setSingleChoiceItems(adapter, -1, new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialogInterface, int i) {
                if (i >= 0) {
                    Button activitiesButton = (Button) findViewById(R.id.activityButton);
                    activityName = (String) activitiesArray[i];
                    activityId = activities.get(i).activityId;
                    activitiesButton.setText(activityName);
                    EditText au = (EditText)findViewById(R.id.activityUnits);
                    au.setText(activities.get(i).activityMeasurementUnits);
                    changes=true;
                }
                dialogInterface.dismiss();
                changes=true;

            }
        });
        AlertDialog dialog = builder.create();
        dialog.show();
    }

    public Date stringToDate(String d){
        Date date = new Date();
        SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd");
        sdf.setTimeZone(TimeZone.getDefault());
        try {
            date = sdf.parse(d);
        } catch (ParseException e) {

        }
        return date;
    }

    public void displayDatePicker(){
        final Dialog dialog = new Dialog(this);
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialog.setContentView(R.layout.dialog_datepicker);

        DatePicker dp = (DatePicker) dialog.findViewById(R.id.datePicker);
        Calendar calActivity = Calendar.getInstance();
        calActivity.setTime(activityDate);
        dp.init(calActivity.get(Calendar.YEAR), calActivity.get(Calendar.MONTH), calActivity.get(Calendar.DAY_OF_MONTH),null);

        Calendar calMax = Calendar.getInstance();
        calMax.setTime(new Date());

        dp.setMaxDate(calMax.getTimeInMillis());

        Button dialogButton = (Button) dialog.findViewById(R.id.okButton);
        dialogButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                DatePicker dp = (DatePicker) dialog.findViewById(R.id.datePicker);
                int day = dp.getDayOfMonth();
                int month = dp.getMonth();
                int year =  dp.getYear();
                Calendar calendar = Calendar.getInstance();
                calendar.set(year, month, day);

                activityDate = calendar.getTime();

                Button cb = (Button)findViewById(R.id.dateButton);
                cb.setText(dateToString(activityDate));
                dialog.dismiss();
                changes=true;
            }
        });
        dialog.show();
    }

    public boolean isNumeric(String str) {
        return str.matches("-?\\d+(\\.\\d+)?");
    }

    public String dateToString(Date d){
        SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd");
        sdf.setTimeZone(TimeZone.getDefault());
        return sdf.format(d);
    }

    public void registerActivity(View v){
        EditText value = (EditText)findViewById(R.id.activityValue);
        String valueText = String.valueOf(value.getText());
        if(activityId>=0) {
            if (isNumeric(valueText)) {
                valueNumber = Float.parseFloat(valueText);

                EditText units = (EditText) findViewById(R.id.activityUnits);
                unitsText = String.valueOf(units.getText());

                if (!unitsText.isEmpty()) {
                    unitsText = unitsText.replaceAll(";", " ");
                    unitsText = unitsText.replaceAll("\\|", " ");
                    unitsText = unitsText.replaceAll("\\*", " ");

                    EditText laborers = (EditText) findViewById(R.id.activityLaborers);
                    String laborersText = String.valueOf(laborers.getText());
                    if (isNumeric(laborersText) || laborersText.isEmpty()) {
                        laborersNumber = laborersText;

                        EditText cost = (EditText) findViewById(R.id.activityCost);
                        String costText = String.valueOf(cost.getText());

                        if (isNumeric(costText) || costText.isEmpty()) {
                            costValue = costText;

                            EditText comments = (EditText) findViewById(R.id.activityComments);
                            commentsText = String.valueOf(comments.getText());

                            if (!commentsText.isEmpty()) {
                                commentsText = commentsText.replaceAll(";", " ");
                                commentsText = commentsText.replaceAll("\\|", " ");
                                commentsText = commentsText.replaceAll("\\*", " ");
                            }

                            if (update.equals("")) {
                                requestCopyToReplications();
                            } else {
                                Toast.makeText(this, "Activity edited successfully", Toast.LENGTH_SHORT).show();
                                Intent i = new Intent(this, manageData.class);
                                i.putExtra("userId", userId);
                                i.putExtra("userRole", userRole);
                                i.putExtra("task", task);
                                i.putExtra("logId", logId);
                                i.putExtra("update", "activity");
                                i.putExtra("activity", activityId);
                                i.putExtra("activityDate", dateToString(activityDate));
                                i.putExtra("activityValue", valueNumber);
                                i.putExtra("activityUnits", unitsText);
                                i.putExtra("activityLaborers", laborersNumber);
                                i.putExtra("activityCost", costValue);
                                i.putExtra("activityComments", commentsText);
                                startActivity(i);
                                finish();
                            }

                        } else {
                            Toast.makeText(this, R.string.enterValidNumberText, Toast.LENGTH_SHORT).show();
                            cost.requestFocus();

                        }
                    } else {
                        Toast.makeText(this, R.string.enterValidNumberText, Toast.LENGTH_SHORT).show();
                        laborers.requestFocus();
                    }
                } else {
                    Toast.makeText(this, R.string.enterValidTextText, Toast.LENGTH_SHORT).show();
                    units.requestFocus();
                }
            } else {
                Toast.makeText(this, R.string.enterValidNumberText, Toast.LENGTH_SHORT).show();
                value.requestFocus();
            }
        } else {
            Toast.makeText(this, R.string.chooseValidActivityText, Toast.LENGTH_SHORT).show();
        }
    }

    public void requestCopyToReplications() {

        AlertDialog.Builder logoutDialog = new AlertDialog.Builder(this);
        logoutDialog.setTitle(R.string.copyRequestTitle);
        logoutDialog.setMessage(R.string.copyRequestString);
        logoutDialog.setNegativeButton(R.string.noButtonText, new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialogInterface, int i) {
                doSave(false);
            }
        });
        logoutDialog.setPositiveButton(R.string.yesButtonText, new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialogInterface, int i) {
                doSave(true);
            }
        });
        logoutDialog.create();
        logoutDialog.show();
    }

    void doSave(boolean copy) {
        Toast.makeText(this, "Activity saved successfully", Toast.LENGTH_SHORT).show();
        Intent i = new Intent(this, chooseFieldPlot.class);
        i.putExtra("userId", userId);
        i.putExtra("userRole", userRole);
        i.putExtra("task", task);
        i.putExtra("title", shortTitle);
        i.putExtra("field", fieldId);
        i.putExtra("plots", plots);
        i.putExtra("newActivity", true);
        i.putExtra("activity", activityId);
        i.putExtra("activityDate", dateToString(activityDate));
        i.putExtra("activityValue", valueNumber);
        i.putExtra("activityUnits", unitsText);
        i.putExtra("activityLaborers", laborersNumber);
        i.putExtra("activityCost", costValue);
        i.putExtra("activityComments", commentsText);
        i.putExtra("copy",copy);
        startActivity(i);
        finish();
    }
}
