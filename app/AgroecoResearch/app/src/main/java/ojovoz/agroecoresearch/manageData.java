package ojovoz.agroecoresearch;

import android.app.AlertDialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v4.content.ContextCompat;
import android.support.v7.app.AppCompatActivity;
import android.util.TypedValue;
import android.view.Gravity;
import android.view.MenuItem;
import android.view.View;
import android.widget.CheckBox;
import android.widget.TableLayout;
import android.widget.TableRow;
import android.widget.TextView;
import android.widget.Toast;

import java.util.ArrayList;
import java.util.Iterator;

/**
 * Created by Eugenio on 17/04/2017.
 */
public class manageData extends AppCompatActivity implements httpConnection.AsyncResponse {

    public int userId;
    public int userRole;
    public String update;
    public String server;

    public agroecoHelper agroHelper;

    public ArrayList<CheckBox> checkboxesLog;
    public ArrayList<CheckBox> checkboxesInputLog;

    private preferenceManager prefs;

    private Context context;
    private ProgressDialog pd;
    boolean bSentSuccessful;

    @Override
    public void onCreate(Bundle savedInstanceState) {

        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_manage_data);

        context = this;

        prefs = new preferenceManager(this);
        server = prefs.getPreference("server");

        agroHelper = new agroecoHelper(this, "crops,fields,treatments,activities,measurements,log,input_log");

        userId = getIntent().getExtras().getInt("userId");
        userRole = getIntent().getExtras().getInt("userRole");
        update = getIntent().getExtras().getString("update");

        if(update.equals("activity")){
            int logId = getIntent().getExtras().getInt("logId");
            String aD = getIntent().getExtras().getString("activityDate");
            Float aV = getIntent().getExtras().getFloat("activityValue");
            String aU = getIntent().getExtras().getString("activityUnits");
            String aL = getIntent().getExtras().getString("activityLaborers");
            String aK = getIntent().getExtras().getString("activityCost");
            String aC = getIntent().getExtras().getString("activityComments");
            agroHelper.updateLogActivityEntry(logId, aD, aV, aU, aL, aK, aC);
        } else if(update.equals("measurement")){
            int logId = getIntent().getExtras().getInt("logId");
            int mS = getIntent().getExtras().getInt("measurementSample");
            String mD = getIntent().getExtras().getString("measurementDate");
            Float mV = getIntent().getExtras().getFloat("measurementValue");
            String mU = getIntent().getExtras().getString("measurementUnits");
            String mC = getIntent().getExtras().getString("measurementCategory");
            String mCC = getIntent().getExtras().getString("measurementComments");
            agroHelper.updateLogMeasurementEntry(logId, mS, mD, mV, mU, mC, mCC);
        } else if(update.equals("cropInput")){
            int inputLogId = getIntent().getExtras().getInt("inputLogId");
            String cD = getIntent().getExtras().getString("cropInputDate");
            String cA = getIntent().getExtras().getString("cropInputAge");
            String cO = getIntent().getExtras().getString("cropInputOrigin");
            String cV = getIntent().getExtras().getString("cropInputVariety");
            Float cQ = getIntent().getExtras().getFloat("cropInputQuantity");
            String cU = getIntent().getExtras().getString("cropInputUnits");
            String cC = getIntent().getExtras().getString("cropInputCost");
            String cCC = getIntent().getExtras().getString("cropInputComments");
            agroHelper.updateInputLogCropEntry(inputLogId, cD, cA, cO, cV, cQ, cU, cC, cCC);
        } else if(update.equals("treatmentInput")){
            int inputLogId = getIntent().getExtras().getInt("inputLogId");
            String tD = getIntent().getExtras().getString("treatmentInputDate");
            String tM = getIntent().getExtras().getString("treatmentInputMaterial");
            Float tQ = getIntent().getExtras().getFloat("treatmentInputQuantity");
            String tU = getIntent().getExtras().getString("treatmentInputUnits");
            String tMM = getIntent().getExtras().getString("treatmentInputMethod");
            String tC = getIntent().getExtras().getString("treatmentInputCost");
            String tCC = getIntent().getExtras().getString("treatmentInputComments");
            agroHelper.updateInputLogTreatmentEntry(inputLogId, tD, tM, tQ, tU, tMM, tC, tCC);
        }

        TextView tt = (TextView)findViewById(R.id.logTableTitle);
        tt.setText(R.string.logTableTitle);

        fillTable();
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
    public boolean onCreateOptionsMenu(android.view.Menu menu) {
        super.onCreateOptionsMenu(menu);
        menu.add(0, 0, 0, R.string.sendMenuText);
        menu.add(1, 1, 1, R.string.deleteMenuText);
        menu.add(2, 2, 2, R.string.invertSelectedMenuText);
        menu.add(3, 3, 3, R.string.opMainMenu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        switch (item.getItemId()) {
            case 0:
                sendSelectedEntries();
                break;
            case 1:
                deleteSelectedEntries();
                break;
            case 2:
                invertSelectedCheckboxes();
                break;
            case 3:
                goToMainMenu();
        }
        return super.onOptionsItemSelected(item);
    }

    @Override public void onBackPressed(){
        final Context context = this;
        Intent i = new Intent(context, mainMenu.class);
        i.putExtra("userId",userId);
        i.putExtra("userRole",userRole);
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

    public void invertSelectedCheckboxes(){
        Iterator<CheckBox> iteratorL = checkboxesLog.iterator();
        while(iteratorL.hasNext()) {
            CheckBox cb = iteratorL.next();
            cb.setChecked(!cb.isChecked());
        }
        Iterator<CheckBox> iteratorIL = checkboxesInputLog.iterator();
        while(iteratorIL.hasNext()) {
            CheckBox cb = iteratorIL.next();
            cb.setChecked(!cb.isChecked());
        }
    }

    public void sendSelectedEntries(){
        String selectedLog="";
        String selectedInputLog="";
        int nSelectedLog=0;
        int nSelectedInputLog=0;
        Iterator<CheckBox> iteratorLog = checkboxesLog.iterator();
        while(iteratorLog.hasNext()) {
            CheckBox cb = iteratorLog.next();
            if(cb.isChecked()){
                selectedLog+=Integer.toString(cb.getId())+",";
                nSelectedLog++;
            }
        }
        Iterator<CheckBox> iteratorInputLog = checkboxesInputLog.iterator();
        while(iteratorInputLog.hasNext()) {
            CheckBox cb = iteratorInputLog.next();
            if(cb.isChecked()){
                selectedInputLog+=Integer.toString(cb.getId())+",";
                nSelectedInputLog++;
            }
        }
        if(!selectedLog.equals("") || !selectedInputLog.equals("")){
            httpConnection http = new httpConnection(this,this);
            if(http.isOnline()) {
                final String mail = prefs.getPreference("mail");
                final String password = prefs.getPreference("password");
                final String smtpServer = prefs.getPreference("smtpServer");
                final String smtpPort = prefs.getPreference("smtpPort");

                if(!mail.equals("") && !password.equals("") && !smtpServer.equals("") && !smtpPort.equals("")) {

                    final boolean allSelectedLog = (nSelectedLog==checkboxesLog.size() && checkboxesLog.size()>0);
                    final boolean allSelectedInputLog = (nSelectedInputLog==checkboxesInputLog.size() && checkboxesInputLog.size()>0);

                    String firstPart="";
                    String secondPart="";

                    if(!selectedLog.equals("")) { firstPart=agroHelper.getSelectedLogItemsAsString(selectedLog); }
                    if(!selectedInputLog.equals("")) { secondPart=agroHelper.getSelectedInputLogItemsAsString(selectedInputLog); }

                    final String body = firstPart + "<>" + secondPart;
                    final String finalSelectedLog = selectedLog;
                    final String finalSelectedInputLog = selectedInputLog;
                    AsyncTask<Void, Void, Void> task = new AsyncTask<Void, Void, Void>() {

                        @Override
                        protected void onPreExecute() {
                            pd = new ProgressDialog(context);
                            pd.setTitle(R.string.sendingDataMessage);
                            pd.setMessage(getString(R.string.pleaseWaitMessage));
                            pd.setCancelable(false);
                            pd.setIndeterminate(true);
                            pd.show();
                            bSentSuccessful=false;
                        }

                        @Override
                        protected Void doInBackground(Void... arg0) {
                            try {
                                Mail m = new Mail(mail, password, smtpServer, smtpPort);
                                String[] toArr = {mail};
                                m.setTo(toArr);
                                m.setFrom(mail);
                                m.setSubject("pA439urcjLVk6szA");
                                m.setBody(body);
                                try {
                                    if(m.send()){
                                        bSentSuccessful=true;
                                    }
                                } catch (Exception e) {
                                    Toast.makeText(context, R.string.unableToSendMessage, Toast.LENGTH_SHORT).show();
                                    if (pd != null) {
                                        pd.dismiss();
                                    }
                                }
                            } catch (Exception e) {

                            }
                            return null;
                        }

                        @Override
                        protected void onPostExecute(Void result) {
                            if (pd!=null) {
                                pd.dismiss();
                            }
                            if(bSentSuccessful) {
                                cleanUp(finalSelectedLog, finalSelectedInputLog, allSelectedLog, allSelectedInputLog, false);
                            }
                        }
                    };
                    task.execute((Void[])null);
                } else {
                    Toast.makeText(this,R.string.invalidParametersMessage,Toast.LENGTH_SHORT).show();
                }
            } else {
                Toast.makeText(this,R.string.pleaseConnectMessage,Toast.LENGTH_SHORT).show();
            }
        } else {
            Toast.makeText(this,R.string.noEntriesSelectedText,Toast.LENGTH_SHORT).show();
        }
    }

    public void deleteSelectedEntries(){
        String selectedLog="";
        String selectedInputLog="";
        Iterator<CheckBox> iteratorLog = checkboxesLog.iterator();
        while(iteratorLog.hasNext()) {
            CheckBox cb = iteratorLog.next();
            if(cb.isChecked()){
                selectedLog+=Integer.toString(cb.getId())+",";
            }
        }
        Iterator<CheckBox> iteratorInputLog = checkboxesInputLog.iterator();
        while(iteratorInputLog.hasNext()) {
            CheckBox cb = iteratorInputLog.next();
            if(cb.isChecked()){
                selectedInputLog+=Integer.toString(cb.getId())+",";
            }
        }
        if(!selectedLog.equals("") || !selectedInputLog.equals("")){
            final String finalSelectedLog = selectedLog;
            final String finalSelectedInputLog = selectedInputLog;
            AlertDialog.Builder logoutDialog = new AlertDialog.Builder(this);
            logoutDialog.setTitle(R.string.deleteAlertTitle);
            logoutDialog.setMessage(R.string.deleteAlertString);
            logoutDialog.setNegativeButton(R.string.cancelButtonText,null);
            logoutDialog.setPositiveButton(R.string.okButtonText, new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialogInterface, int i) {
                    doDelete(finalSelectedLog, finalSelectedInputLog, true);
                }
            });
            logoutDialog.create();
            logoutDialog.show();
        } else {
            Toast.makeText(this,R.string.noEntriesSelectedText,Toast.LENGTH_SHORT).show();
        }
    }

    public void doDelete(String selectedLog, String selectedInputLog, boolean deleteFromCalendar){
        if(!selectedLog.equals("")) {
            deleteMeasuredPlots(selectedLog);
            agroHelper.deleteLogEntries(selectedLog, deleteFromCalendar);
        }
        if(!selectedInputLog.equals("")) {
            agroHelper.deleteInputLogEntries(selectedInputLog, deleteFromCalendar);
        }
        fillTable();
    }

    public void cleanUp(String selectedLog, String selectedInputLog, boolean allSelectedLog, boolean allSelectedInputLog, boolean deleteFromCalendar){
        if(!selectedLog.equals("")) {
            deleteMeasuredPlots(selectedLog);
            agroHelper.deleteLogEntries(selectedLog, deleteFromCalendar);
        }
        if(!selectedInputLog.equals("")) {
            agroHelper.deleteInputLogEntries(selectedInputLog, deleteFromCalendar);
        }
        if(allSelectedLog && allSelectedInputLog){
            final Context context = this;
            Intent i = new Intent(context, mainMenu.class);
            i.putExtra("userId",userId);
            i.putExtra("userRole",userRole);
            startActivity(i);
            finish();
        } else {
            fillTable();
        }
    }

    public void deleteMeasuredPlots(String e){
        String mpSaved=prefs.getPreference("measuredPlots");
        if(!mpSaved.isEmpty()) {
            String[] entries = e.split(",");
            for (int i = 0; i < entries.length; i++) {
                int id = Integer.parseInt(entries[i]);
                oLog l = agroHelper.getLogItemFromId(id);
                if(l.logMeasurementId>0) {
                    oMeasuredPlotHelper mp = agroHelper.getMeasuredPlotFromLogId(l.logId, l.logFieldId, l.logMeasurementId, l.logPlots);
                    if (mp.fieldId >= 0) {
                        mpSaved = prefs.getPreference("measuredPlots");
                        String mpNew = "";
                        String mpList[] = mpSaved.split(";");
                        for (int j = 0; j < mpList.length; j++) {
                            String mpElements[] = mpList[j].split(",");
                            if (mp.fieldId == Integer.valueOf(mpElements[0]) && mp.plotNumber == Integer.valueOf(mpElements[1]) && mp.measurementId == Integer.valueOf(mpElements[2])) {

                            } else {
                                if (mpNew.isEmpty()) {
                                    mpNew = mpList[j];
                                } else {
                                    mpNew = mpNew + ";" + mpList[j];
                                }
                            }
                        }
                        prefs.savePreference("measuredPlots", mpNew);
                    }
                }
            }
        }
    }

    public void fillTable(){
        checkboxesLog = new ArrayList<>();
        checkboxesInputLog = new ArrayList<>();
        TableLayout logTable = (TableLayout) findViewById(R.id.logTable);
        logTable.removeAllViews();

        int n = 0;
        int m = 0;

        ArrayList<oLog> filteredLog = agroHelper.log;

        if(filteredLog.size()>0) {

            final TableRow trowAM = new TableRow(manageData.this);
            TableRow.LayoutParams lpAM = new TableRow.LayoutParams(TableRow.LayoutParams.MATCH_PARENT, TableRow.LayoutParams.MATCH_PARENT, 1.0f);
            lpAM.setMargins(10, 0, 0, 0);
            trowAM.setBackgroundColor(ContextCompat.getColor(this, R.color.colorPrimary));

            TextView tvAM = new TextView(manageData.this);
            tvAM.setTextColor(ContextCompat.getColor(this, R.color.colorWhite));
            tvAM.setTextSize(TypedValue.COMPLEX_UNIT_DIP, 20f);
            tvAM.setText(R.string.activitiesMeasurementsTableTitle);
            tvAM.setTextAlignment(View.TEXT_ALIGNMENT_TEXT_START);

            TableRow.LayoutParams lpAC = new TableRow.LayoutParams(TableRow.LayoutParams.MATCH_PARENT, TableRow.LayoutParams.MATCH_PARENT, 1.0f);
            lpAC.setMargins(10, 10, 0, 10);
            lpAC.span=2;
            lpAC.weight=1;
            tvAM.setLayoutParams(lpAC);

            trowAM.addView(tvAM);

            trowAM.setGravity(Gravity.CENTER_VERTICAL);
            logTable.addView(trowAM, lpAM);

            Iterator<oLog> logIterator = filteredLog.iterator();
            while (logIterator.hasNext()) {
                oLog logElement = logIterator.next();
                if(logElement.logUserId==userId || userRole>0) {
                    final TableRow trow = new TableRow(manageData.this);
                    TableRow.LayoutParams lp = new TableRow.LayoutParams(TableRow.LayoutParams.MATCH_PARENT, TableRow.LayoutParams.MATCH_PARENT, 1.0f);
                    lp.setMargins(10, 10, 0, 10);

                    if (n % 2 == 0) {
                        trow.setBackgroundColor(ContextCompat.getColor(this, R.color.lightGray));
                    } else {
                        trow.setBackgroundColor(ContextCompat.getColor(this, R.color.colorWhite));
                    }

                    String itemName = "";
                    if (logElement.logActivityId > 0) {
                        itemName = agroHelper.getActivityNameFromId(logElement.logActivityId);
                    } else if (logElement.logMeasurementId > 0) {
                        itemName = agroHelper.getMeasurementNameFromId(logElement.logMeasurementId) + " (" + agroHelper.getMeasurementCategoryFromId(logElement.logMeasurementId) + ")";
                    }

                    String tvText = agroHelper.dateToString(logElement.logDate) + " " + agroHelper.getFieldNameFromId(logElement.logFieldId)
                            + "\n" + itemName;

                    CheckBox cb = new CheckBox(manageData.this);
                    cb.setButtonDrawable(R.drawable.custom_checkbox);
                    cb.setId(logElement.logId);
                    cb.setPadding(4, 4, 4, 4);
                    cb.setChecked(true);
                    checkboxesLog.add(cb);
                    trow.addView(cb, lp);

                    TextView tv = new TextView(manageData.this);
                    tv.setId(logElement.logId);
                    tv.setTextColor(ContextCompat.getColor(this, R.color.colorPrimary));
                    tv.setTextSize(TypedValue.COMPLEX_UNIT_DIP, 17f);
                    tv.setText(tvText);
                    tv.setTextAlignment(View.TEXT_ALIGNMENT_TEXT_START);
                    tv.setPadding(0, 10, 0, 10);
                    tv.setMaxWidth(350);
                    tv.setOnClickListener(new View.OnClickListener() {

                        @Override
                        public void onClick(View v) {
                            viewItem(v, v.getId());
                        }

                    });
                    trow.addView(tv, lp);

                    trow.setGravity(Gravity.CENTER_VERTICAL);
                    logTable.addView(trow, lp);

                    n++;
                }
            }
        }

        ArrayList<oInputLog> filteredInputLog = agroHelper.inputLog;

        if(filteredInputLog.size()>0) {

            final TableRow trowI = new TableRow(manageData.this);
            TableRow.LayoutParams lpI = new TableRow.LayoutParams(TableRow.LayoutParams.MATCH_PARENT, TableRow.LayoutParams.MATCH_PARENT, 1.0f);
            lpI.setMargins(10, 0, 0, 0);
            trowI.setBackgroundColor(ContextCompat.getColor(this, R.color.colorPrimary));

            TextView tvI = new TextView(manageData.this);
            tvI.setTextColor(ContextCompat.getColor(this, R.color.colorWhite));
            tvI.setTextSize(TypedValue.COMPLEX_UNIT_DIP, 20f);
            tvI.setText(R.string.inputsTableTitle);
            tvI.setTextAlignment(View.TEXT_ALIGNMENT_TEXT_START);

            TableRow.LayoutParams lpIC = new TableRow.LayoutParams(TableRow.LayoutParams.MATCH_PARENT, TableRow.LayoutParams.MATCH_PARENT, 1.0f);
            lpIC.setMargins(10, 10, 0, 10);
            lpIC.span=2;
            lpIC.weight=1;
            tvI.setLayoutParams(lpIC);
            trowI.addView(tvI);

            trowI.setGravity(Gravity.CENTER_VERTICAL);
            logTable.addView(trowI, lpI);

            Iterator<oInputLog> logIterator = filteredInputLog.iterator();
            while (logIterator.hasNext()) {
                oInputLog inputLogElement = logIterator.next();
                if(inputLogElement.inputLogUserId==userId || userRole>0) {
                    final TableRow trow = new TableRow(manageData.this);
                    TableRow.LayoutParams lp = new TableRow.LayoutParams(TableRow.LayoutParams.MATCH_PARENT, TableRow.LayoutParams.MATCH_PARENT, 1.0f);
                    lp.setMargins(10, 10, 0, 10);

                    if (n % 2 == 0) {
                        trow.setBackgroundColor(ContextCompat.getColor(this, R.color.lightGray));
                    } else {
                        trow.setBackgroundColor(ContextCompat.getColor(this, R.color.colorWhite));
                    }

                    String itemName = "";
                    if (inputLogElement.inputLogCropId > 0) {
                        itemName = agroHelper.getCropNameFromId(inputLogElement.inputLogCropId);
                    } else if (inputLogElement.inputLogTreatmentId > 0) {
                        itemName = agroHelper.getTreatmentNameFromId(inputLogElement.inputLogTreatmentId);
                    }

                    String tvText = agroHelper.dateToString(inputLogElement.inputLogDate) + " " + agroHelper.getFieldNameFromId(inputLogElement.inputLogFieldId)
                            + "\n" + itemName;

                    CheckBox cb = new CheckBox(manageData.this);
                    cb.setButtonDrawable(R.drawable.custom_checkbox);
                    cb.setId(inputLogElement.inputLogId);
                    cb.setPadding(10, 10, 10, 10);
                    cb.setChecked(true);
                    checkboxesInputLog.add(cb);
                    trow.addView(cb, lp);

                    TextView tv = new TextView(manageData.this);
                    tv.setId(inputLogElement.inputLogId);
                    tv.setTextColor(ContextCompat.getColor(this, R.color.colorPrimary));
                    tv.setTextSize(TypedValue.COMPLEX_UNIT_DIP, 17f);
                    tv.setText(tvText);
                    tv.setTextAlignment(View.TEXT_ALIGNMENT_TEXT_START);
                    tv.setPadding(0, 10, 0, 10);
                    tv.setMaxWidth(350);
                    tv.setOnClickListener(new View.OnClickListener() {

                        @Override
                        public void onClick(View v) {
                            viewInput(v, v.getId());
                        }

                    });
                    trow.addView(tv, lp);

                    trow.setGravity(Gravity.CENTER_VERTICAL);
                    logTable.addView(trow, lp);

                    n++;
                }
            }
        }


        if(n==0 && m==0){
            TextView tt = (TextView)findViewById(R.id.logTableTitle);
            tt.setText(R.string.noLoggedDataMessage);
        }
    }

    public void viewInput(View v, int id){
        TextView tv = (TextView)v;
        tv.setBackgroundColor(ContextCompat.getColor(this,R.color.colorPrimaryDark));

        oInputLog inputLogItem = agroHelper.getInputLogItemFromId(id);
        oField lf = agroHelper.getFieldFromId(inputLogItem.inputLogFieldId);

        String inputTitle="";


        inputTitle = "Field: " + lf.fieldName + " R" + Integer.toString(lf.fieldReplicationN) + "\nPlots: " + agroHelper.getPlotNames(lf,inputLogItem.inputLogPlots)+"\n";



        if(inputLogItem.inputLogCropId>0){
            inputTitle+=agroHelper.getCropNameFromId(inputLogItem.inputLogCropId);
            final Context context = this;
            Intent i = new Intent(context, enterCropInput.class);
            i.putExtra("userId", userId);
            i.putExtra("userRole", userRole);
            i.putExtra("task", "input");
            i.putExtra("inputLogId",inputLogItem.inputLogId);
            i.putExtra("field", lf.fieldId);
            i.putExtra("plots", inputLogItem.inputLogPlots);
            i.putExtra("update","crop");
            i.putExtra("cropId", inputLogItem.inputLogCropId);
            i.putExtra("cropInputDate", agroHelper.dateToString(inputLogItem.inputLogDate));
            i.putExtra("cropInputAge", inputLogItem.inputLogInputAge);
            i.putExtra("cropInputOrigin", inputLogItem.inputLogInputOrigin);
            i.putExtra("cropInputVariety", inputLogItem.inputLogCropVariety);
            i.putExtra("cropInputQuantity", inputLogItem.inputLogInputQuantity);
            i.putExtra("cropInputUnits", inputLogItem.inputLogInputUnits);
            i.putExtra("cropInputCost", inputLogItem.inputLogInputCost);
            i.putExtra("cropInputComments", inputLogItem.inputLogComments);
            i.putExtra("title",inputTitle);
            startActivity(i);
            finish();
        } else if(inputLogItem.inputLogTreatmentId>0){
            inputTitle+=agroHelper.getTreatmentNameFromId(inputLogItem.inputLogTreatmentId);
            final Context context = this;
            Intent i = new Intent(context, enterTreatmentInput.class);
            i.putExtra("userId", userId);
            i.putExtra("userRole", userRole);
            i.putExtra("task", "input");
            i.putExtra("inputLogId",inputLogItem.inputLogId);
            i.putExtra("field", lf.fieldId);
            i.putExtra("plots", inputLogItem.inputLogPlots);
            i.putExtra("update","treatment");
            i.putExtra("treatmentId", inputLogItem.inputLogTreatmentId);
            i.putExtra("treatmentInputDate", agroHelper.dateToString(inputLogItem.inputLogDate));
            i.putExtra("treatmentInputMaterial", inputLogItem.inputLogTreatmentMaterial);
            i.putExtra("treatmentInputQuantity", inputLogItem.inputLogInputQuantity);
            i.putExtra("treatmentInputUnits", inputLogItem.inputLogInputUnits);
            i.putExtra("treatmentInputMethod", inputLogItem.inputLogTreatmentPreparationMethod);
            i.putExtra("treatmentInputCost", inputLogItem.inputLogInputCost);
            i.putExtra("treatmentInputComments", inputLogItem.inputLogComments);
            i.putExtra("title",inputTitle);
            startActivity(i);
            finish();
        }
    }

    public void viewItem(View v,int id){
        TextView tv = (TextView)v;
        tv.setBackgroundColor(ContextCompat.getColor(this,R.color.colorPrimaryDark));

        oLog logItem = agroHelper.getLogItemFromId(id);

        oField lf = agroHelper.getFieldFromId(logItem.logFieldId);


        if(logItem.logActivityId>0) {

            String activityTitle = "Field: " + lf.fieldName + " R" + Integer.toString(lf.fieldReplicationN) + "\nPlots: " + agroHelper.getPlotNames(lf,logItem.logPlots) + "\n" + agroHelper.getActivityNameFromId(logItem.logActivityId);

            final Context context = this;
            Intent i = new Intent(context, enterActivity.class);
            i.putExtra("userId", userId);
            i.putExtra("userRole", userRole);
            i.putExtra("logId",logItem.logId);
            i.putExtra("field", logItem.logFieldId);
            i.putExtra("plots", logItem.logPlots);
            i.putExtra("activity",logItem.logActivityId);
            i.putExtra("update", "activity");
            i.putExtra("title",activityTitle);
            i.putExtra("units",logItem.logValueUnits);
            i.putExtra("date",agroHelper.dateToString(logItem.logDate));
            i.putExtra("activityValue",logItem.logNumberValue);
            i.putExtra("activityLaborers",logItem.logLaborers);
            i.putExtra("activityCost",logItem.logCost);
            i.putExtra("activityComments",logItem.logComments);
            startActivity(i);
            finish();

        } else if(logItem.logMeasurementId>0) {

            String measurementTitle = "Field: " + lf.fieldName + " R" + Integer.toString(lf.fieldReplicationN) + "\nPlots: " + agroHelper.getPlotNames(lf,logItem.logPlots) + "\n" + agroHelper.getMeasurementNameFromId(logItem.logMeasurementId)  + " (" + agroHelper.getMeasurementCategoryFromId(logItem.logMeasurementId) + ")";

            oMeasurement m = agroHelper.getMeasurementFromId(logItem.logMeasurementId);

            final Context context = this;
            Intent i = new Intent(context, enterMeasurement.class);
            i.putExtra("userId", userId);
            i.putExtra("userRole", userRole);
            i.putExtra("logId",logItem.logId);
            i.putExtra("fieldId", logItem.logFieldId);
            i.putExtra("plots", logItem.logPlots);
            i.putExtra("measurement",logItem.logMeasurementId);
            i.putExtra("type",m.measurementType);
            i.putExtra("update", "measurement");
            i.putExtra("title",measurementTitle);
            i.putExtra("hasSamples",m.measurementHasSampleNumber);
            i.putExtra("min",m.measurementMin);
            i.putExtra("max",m.measurementMax);
            i.putExtra("categories",m.measurementCategories);
            i.putExtra("measurementDate",agroHelper.dateToString(logItem.logDate));
            i.putExtra("measurementValue",logItem.logNumberValue);
            i.putExtra("units",logItem.logValueUnits);
            i.putExtra("measurementCategory",logItem.logTextValue);
            i.putExtra("measurementComments",logItem.logComments);
            startActivity(i);
            finish();

        }
    }

    @Override
    public void processFinish(String output){

    }

    @Override
    protected void onDestroy() {
        if (pd!=null) {
            pd.dismiss();
        }
        super.onDestroy();
    }
}
