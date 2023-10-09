package ojovoz.agroecoresearch;

import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.support.v4.content.ContextCompat;
import android.support.v7.app.AppCompatActivity;
import android.util.TypedValue;
import android.view.Gravity;
import android.view.View;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.ListAdapter;
import android.widget.TableLayout;
import android.widget.TableRow;
import android.widget.TextView;

import java.util.ArrayList;
import java.util.Iterator;

/**
 * Created by Eugenio on 09/01/2018.
 */
public class enterHealthReport extends AppCompatActivity {

    public boolean changes=false;
    public int exitAction;

    public agroecoHelper agroHelper;

    public ArrayList<oHealthReport> healthReportItems;

    public int userId;
    public int userRole;
    public int logId;
    public String task;
    public int fieldId;
    public String plots;
    public int measurementId;
    public boolean hasSamples;
    public String measurementTitle;
    public String shortTitle;
    public String measurementChosenCategory;
    public String measurementCategory;
    public String categoriesString;
    public String measurementDate;
    public String measurementUnits;
    public int type;
    public float min;
    public float max;
    public String update;
    public String commentsText;
    public int sampleNumber;
    public int sampleId;
    public String previousValue;

    ArrayList<Button> buttons;
    Button currentChosenButton;

    ArrayList<String> chosenValues;
    ArrayList<CheckBox> checkboxes;

    private promptDialog dlg=null;

    @Override
    public void onCreate(Bundle savedInstanceState) {

        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_enter_health_report);

        agroHelper = new agroecoHelper(this,"");
        agroHelper.createHealthReportItems();
        healthReportItems = agroHelper.healthReportItems;

        userId = getIntent().getExtras().getInt("userId");
        userRole = getIntent().getExtras().getInt("userRole");
        logId = getIntent().getExtras().getInt("logId");
        task = getIntent().getExtras().getString("task");
        fieldId = getIntent().getExtras().getInt("field");
        plots = getIntent().getExtras().getString("plots");
        measurementId = getIntent().getExtras().getInt("measurement");
        hasSamples = getIntent().getExtras().getBoolean("hasSamples");
        measurementTitle = getIntent().getExtras().getString("title");
        shortTitle = getIntent().getExtras().getString("shortTitle");
        measurementChosenCategory = getIntent().getExtras().getString("measurementChosenCategory");
        measurementDate = getIntent().getExtras().getString("measurementDate");
        measurementUnits = getIntent().getExtras().getString("units");
        type = getIntent().getExtras().getInt("type");
        min = getIntent().getExtras().getFloat("min");
        max = getIntent().getExtras().getFloat("max");
        categoriesString = getIntent().getExtras().getString("categories");
        measurementCategory = getIntent().getExtras().getString("measurementCategory");
        update = getIntent().getExtras().getString("update");
        commentsText = getIntent().getExtras().getString("commentsText");
        update = getIntent().getExtras().getString("update");
        sampleNumber = getIntent().getExtras().getInt("sampleNumber");
        sampleId = getIntent().getExtras().getInt("sampleId");

        previousValue = getIntent().getExtras().getString("previousValue");

        TextView tt = (TextView) findViewById(R.id.fieldPlotSampleText);
        tt.setText(measurementTitle+"\nSample: "+String.valueOf(sampleNumber));

        fillItemsTable(previousValue);
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

    @Override public void onBackPressed () {
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
                        //goToDataManager();
                        break;
                    case 2:
                        //goToMainMenu();
                        break;
                }
            }
        });
        logoutDialog.create();
        logoutDialog.show();
    }

    public void goBack(){
        final Context context = this;
        Intent i = new Intent(context, enterMeasurement.class);

        i.putExtra("userId", userId);
        i.putExtra("userRole", userRole);
        i.putExtra("logId", logId);
        i.putExtra("task", task);
        i.putExtra("title", measurementTitle);
        i.putExtra("shortTitle",shortTitle);
        i.putExtra("measurementChosenCategory", measurementChosenCategory);
        i.putExtra("field", fieldId);
        i.putExtra("plots", plots);
        i.putExtra("measurement", measurementId);
        i.putExtra("type",type);
        i.putExtra("min",min);
        i.putExtra("max",max);
        i.putExtra("categories",categoriesString);
        i.putExtra("measurementDate", measurementDate);
        i.putExtra("measurementCategory", measurementCategory);
        i.putExtra("units", measurementUnits);
        i.putExtra("measurementComments", commentsText);
        i.putExtra("hasSamples",hasSamples);
        i.putExtra("update",update);
        i.putExtra("sampleNumber",sampleNumber);
        i.putExtra("sampleId",sampleId);
        i.putExtra("healthReportValues",previousValue);

        startActivity(i);
        finish();
    }

    public void fillItemsTable(String previousValue){
        buttons = new ArrayList<>();
        chosenValues = new ArrayList<>();
        checkboxes = new ArrayList<>();

        String[] previousValues = null;

        if(previousValue!=null){
            previousValues = previousValue.split("\\#");
        }

        TableLayout itemsTable = (TableLayout) findViewById(R.id.itemsTable);
        Iterator<oHealthReport> iterator = healthReportItems.iterator();
        int n=0;
        while (iterator.hasNext()) {
            oHealthReport hi = iterator.next();

            final TableRow trow = new TableRow(enterHealthReport.this);
            TableRow.LayoutParams lp = new TableRow.LayoutParams(TableRow.LayoutParams.MATCH_PARENT, TableRow.LayoutParams.MATCH_PARENT, 1.0f);
            lp.setMargins(10, 10, 0, 10);
            if (n % 2 == 0) {
                trow.setBackgroundColor(ContextCompat.getColor(this, R.color.lightGray));
            } else {
                trow.setBackgroundColor(ContextCompat.getColor(this, R.color.colorWhite));
            }

            CheckBox cb = new CheckBox(enterHealthReport.this);
            cb.setButtonDrawable(R.drawable.custom_checkbox);
            cb.setId(n);
            cb.setPadding(0, 10, 0, 10);
            cb.setMinWidth(60);
            cb.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(final View view) {
                    view.requestFocus();
                    CheckBox cb = (CheckBox)view;
                    toggleButtonVisibility(cb);
                    changes=true;
                }
            });
            if(previousValues.length==healthReportItems.size()){
                if(!previousValues[n].trim().isEmpty()){
                    cb.setChecked(true);
                }
            }
            checkboxes.add(cb);
            trow.addView(cb, lp);

            TextView tv = new TextView(enterHealthReport.this);
            tv.setId(n);
            tv.setTextColor(ContextCompat.getColor(this,R.color.colorPrimary));
            tv.setText(hi.itemName);
            tv.setTextSize(TypedValue.COMPLEX_UNIT_DIP,20f);
            tv.setPadding(0,15,0,0);
            tv.setMaxWidth(200);
            trow.addView(tv, lp);

            Button sb = new Button(enterHealthReport.this);
            sb.setTextSize(TypedValue.COMPLEX_UNIT_DIP, 17f);
            sb.setText(R.string.chooseValueSampleTable);
            sb.setTextAlignment(View.TEXT_ALIGNMENT_CENTER);
            sb.setPadding(10,10,0,10);
            sb.setId(n);
            sb.setBackgroundResource(R.drawable.button_background);
            sb.setTextColor(Color.WHITE);
            sb.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    currentChosenButton = (Button) v;
                    showHealthCategoriesTableItem();
                    changes = true;
                }
            });
            if(previousValues.length==healthReportItems.size()){
                if(!previousValues[n].trim().isEmpty()){
                    if(previousValues[n].length()>10){
                        sb.setText(previousValues[n].substring(0,9)+" …");
                    } else {
                        sb.setText(previousValues[n]);
                    }
                    chosenValues.add(previousValues[n]);
                } else {
                    sb.setVisibility(View.INVISIBLE);
                    chosenValues.add(" ");
                }
            } else {
                sb.setVisibility(View.INVISIBLE);
                chosenValues.add(" ");
            }
            buttons.add(sb);
            trow.addView(sb, lp);

            trow.setGravity(Gravity.CENTER_VERTICAL);
            itemsTable.addView(trow, lp);
            n++;
        }
    }

    public void toggleButtonVisibility(CheckBox cb){
        int id=cb.getId();
        Button b = buttons.get(id);
        b.setVisibility((cb.isChecked()) ? View.VISIBLE : View.INVISIBLE);
    }

    public void showHealthCategoriesTableItem(){
        int item = currentChosenButton.getId();

        final String[] categoriesArray = healthReportItems.get(item).categories;

        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        builder.setCancelable(true);
        builder.setNegativeButton(R.string.cancelButtonText, new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int which) {
                dialog.dismiss();
            }
        });

        final ListAdapter adapter = new ArrayAdapter<>(this, R.layout.checked_list_template, categoriesArray);
        builder.setSingleChoiceItems(adapter, -1, new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialogInterface, int i) {
                if (i >= 0) {
                    String itemCategory = (String) categoriesArray[i];
                    if (itemCategory.equals(getString(R.string.otherListText))) {
                        String value = chosenValues.get(currentChosenButton.getId());
                        dlg = new promptDialog(enterHealthReport.this, R.string.emptyString, R.string.enterOtherValueLabel, value) {
                            @Override
                            public boolean onOkClicked(String input) {
                                String display="";
                                input = input.replaceAll(";","");
                                input = input.replaceAll("\\|","");
                                input = input.replaceAll("\\*","");
                                input = input.replaceAll("\\#","");
                                if(input.length()>10){
                                    display=input.substring(0,9)+" …";
                                } else {
                                    display=input;
                                }
                                currentChosenButton.setText(display);
                                chosenValues.set(currentChosenButton.getId(),input);
                                return true;
                            }
                        };
                        dlg.show();
                    } else {
                        String display="";
                        String chosen=categoriesArray[i];
                        if(chosen.length()>10){
                            display=chosen.substring(0,9)+" …";
                        } else {
                            display=chosen;
                        }
                        currentChosenButton.setText(display);
                        chosenValues.set(currentChosenButton.getId(),chosen);
                    }
                }
                dialogInterface.dismiss();
            }
        });
        AlertDialog dialog = builder.create();
        dialog.show();
    }

    public void registerHealthReport(View v){
        String itemsString="";
        String item="";
        Iterator<CheckBox> iterator = checkboxes.iterator();
        while (iterator.hasNext()) {
            CheckBox cb = iterator.next();
            int index = cb.getId();
            item=" #";
            if(cb.isChecked()){
                item=chosenValues.get(index)+"#";
            }
            if(itemsString.isEmpty()){
                itemsString=item;
            } else {
                itemsString=itemsString+item;
            }
        }

        itemsString = itemsString.substring(0,itemsString.length()-1);

        final Context context = this;
        Intent i = new Intent(context, enterMeasurement.class);

        i.putExtra("userId", userId);
        i.putExtra("userRole", userRole);
        i.putExtra("logId",logId);
        i.putExtra("task", task);
        i.putExtra("title", measurementTitle);
        i.putExtra("shortTitle",shortTitle);
        i.putExtra("measurementChosenCategory", measurementChosenCategory);
        i.putExtra("field", fieldId);
        i.putExtra("plots", plots);
        i.putExtra("measurement", measurementId);
        i.putExtra("type",type);
        i.putExtra("min",min);
        i.putExtra("max",max);
        i.putExtra("categories",categoriesString);
        i.putExtra("measurementDate", measurementDate);
        i.putExtra("measurementCategory", measurementCategory);
        i.putExtra("units", measurementUnits);
        i.putExtra("measurementComments", commentsText);
        i.putExtra("update",update);
        i.putExtra("hasSamples",hasSamples);
        i.putExtra("sampleNumber", sampleNumber);
        i.putExtra("sampleId", sampleId);
        i.putExtra("healthReportValues",itemsString);

        startActivity(i);
        finish();

    }
}
