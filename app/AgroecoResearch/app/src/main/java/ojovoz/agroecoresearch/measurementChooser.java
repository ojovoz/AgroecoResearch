package ojovoz.agroecoresearch;

import android.app.AlertDialog;
import android.app.Dialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.Bundle;
import android.support.v4.content.ContextCompat;
import android.support.v7.app.AppCompatActivity;
import android.util.TypedValue;
import android.view.Gravity;
import android.view.MenuItem;
import android.view.View;
import android.view.Window;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.ListAdapter;
import android.widget.TableLayout;
import android.widget.TableRow;
import android.widget.TextView;

import java.lang.reflect.Array;
import java.util.ArrayList;
import java.util.Iterator;

/**
 * Created by Eugenio on 18/08/2017.
 */
public class measurementChooser extends AppCompatActivity {

    public int userId;
    public int userRole;

    public agroecoHelper agroHelper;

    public CharSequence categoriesArray[];
    public ArrayList<String> measurementCategories;
    public String chosenCategory;
    public ArrayList<oMeasurement> measurements;

    public int[] measurementIds;

    @Override
    public void onCreate(Bundle savedInstanceState) {

        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_measurement_chooser);

        userId = getIntent().getExtras().getInt("userId");
        userRole = getIntent().getExtras().getInt("userRole");

        String measurementCategory=getIntent().getExtras().getString("measurementChosenCategory");

        TableLayout tl = (TableLayout) findViewById(R.id.measurementTable);
        tl.setVisibility(View.INVISIBLE);

        agroHelper = new agroecoHelper(this, "crops,fields,treatments,measurements");

        measurementCategories = agroHelper.getMeasurementCategories(userRole);
        categoriesArray=measurementCategories.toArray(new CharSequence[measurementCategories.size()]);

        Button categoryListView = (Button) findViewById(R.id.chooseCategoryButton);

        categoryListView.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                switch (v.getId()) {
                    case R.id.chooseCategoryButton:
                        showSelectCategoriesDialog();
                        break;
                    default:
                        break;
                }
            }
        });

        if(measurementCategory!=null){
            chosenCategory=measurementCategory;
            Button fieldListView = (Button) findViewById(R.id.chooseCategoryButton);
            fieldListView.setText(chosenCategory);
            fillTable();
        }
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
                goToDataManager();
                break;
            case 1:
                goToMainMenu();
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

    public void showSelectCategoriesDialog(){
        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        builder.setCancelable(true);
        builder.setNegativeButton(R.string.cancelButtonText, new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int which) {
                dialog.dismiss();
            }
        });
        final ListAdapter adapter = new ArrayAdapter<>(this,R.layout.checked_list_template,categoriesArray);
        builder.setSingleChoiceItems(adapter,-1,new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialogInterface, int i) {
                if(i>=0) {
                    chosenCategory=measurementCategories.get(i);
                    Button fieldListView = (Button) findViewById(R.id.chooseCategoryButton);
                    fieldListView.setText(chosenCategory);
                    fillTable();
                }
                dialogInterface.dismiss();

            }
        });
        AlertDialog dialog = builder.create();
        dialog.show();
    }

    public void fillTable(){
        TableLayout tl = (TableLayout) findViewById(R.id.measurementTable);
        tl.setVisibility(View.VISIBLE);

        TableLayout chooserTable = (TableLayout) findViewById(R.id.chooserTable);
        chooserTable.removeAllViews();

        String subCategory="";

        measurements = agroHelper.getMeasurements(null,null,userRole);
        measurementIds = new int[measurements.size()];

        Iterator<oMeasurement> iterator = measurements.iterator();
        int n=0;
        while (iterator.hasNext()) {
            oMeasurement measurement = iterator.next();

            if(measurement.measurementCategory.equals(chosenCategory)) {

                if (!measurement.measurementSubCategory.equals(subCategory)) {
                    final TableRow trow = new TableRow(measurementChooser.this);
                    TableRow.LayoutParams lp = new TableRow.LayoutParams(TableRow.LayoutParams.MATCH_PARENT, TableRow.LayoutParams.MATCH_PARENT, 1.0f);
                    lp.setMargins(4, 4, 4, 4);
                    trow.setBackgroundColor(ContextCompat.getColor(this, R.color.colorPrimary));
                    TextView tv = new TextView(measurementChooser.this);
                    tv.setTextColor(ContextCompat.getColor(this, R.color.colorWhite));
                    tv.setText(measurement.measurementSubCategory);
                    tv.setTextSize(TypedValue.COMPLEX_UNIT_DIP, 20f);
                    tv.setPadding(4, 4, 4, 4);
                    trow.addView(tv, lp);
                    trow.setGravity(Gravity.CENTER_VERTICAL);
                    chooserTable.addView(trow, lp);
                    subCategory = measurement.measurementSubCategory;
                }

                final TableRow trow = new TableRow(measurementChooser.this);
                TableRow.LayoutParams lpRow = new TableRow.LayoutParams(TableRow.LayoutParams.MATCH_PARENT, TableRow.LayoutParams.MATCH_PARENT, 1.0f);
                lpRow.setMargins(4, 4, 4, 4);
                TableRow.LayoutParams lpCb = new TableRow.LayoutParams(TableRow.LayoutParams.MATCH_PARENT, TableRow.LayoutParams.MATCH_PARENT, 0.2f);
                lpCb.setMargins(4, 4, 4, 4);
                TableRow.LayoutParams lpTv = new TableRow.LayoutParams(TableRow.LayoutParams.MATCH_PARENT, TableRow.LayoutParams.MATCH_PARENT, 0.8f);
                lpTv.setMargins(4, 4, 4, 4);

                if (n % 2 == 0) {
                    trow.setBackgroundColor(ContextCompat.getColor(this, R.color.lightGray));
                } else {
                    trow.setBackgroundColor(ContextCompat.getColor(this, R.color.colorWhite));
                }

                CheckBox cb = new CheckBox(measurementChooser.this);
                cb.setButtonDrawable(R.drawable.info_checkbox);
                cb.setId(n);
                cb.setPadding(4, 4, 4, 4);
                cb.setChecked(false);
                cb.setMinWidth(60);
                cb.setOnClickListener(new View.OnClickListener(){
                    @Override
                    public void onClick(View v) {
                        showDescription(v.getId(), v);
                    }
                });
                trow.addView(cb, lpCb);

                TextView tv = new TextView(measurementChooser.this);
                tv.setId(n);
                tv.setTextColor(ContextCompat.getColor(this, R.color.colorPrimary));
                tv.setText(measurement.measurementName);
                tv.setTextSize(TypedValue.COMPLEX_UNIT_DIP, 20f);
                tv.setPadding(4, 4, 4, 4);
                tv.setMaxWidth(350);
                tv.setOnClickListener(new View.OnClickListener() {

                    @Override
                    public void onClick(View v) {
                        chooseItem(v.getId(), v);
                    }

                });
                trow.addView(tv, lpTv);
                trow.setGravity(Gravity.CENTER_VERTICAL);
                chooserTable.addView(trow, lpRow);

                measurementIds[n]=measurement.measurementId;
                n++;
            }
        }
    }

    public void showDescription(int id, View v){

        CheckBox cb = (CheckBox)v;
        cb.setChecked(false);

        final Dialog dialog = new Dialog(this);
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialog.setContentView(R.layout.dialog_description);
        dialog.setCanceledOnTouchOutside(true);
        TextView descriptionTitle = (TextView)dialog.findViewById(R.id.description_title);
        descriptionTitle.setText("Measurement: "+agroHelper.getMeasurementNameFromId(measurementIds[id]));

        TextView descriptionText = (TextView)dialog.findViewById(R.id.description_text);
        String activityDescription = agroHelper.getMeasurementDescriptionFromId(measurementIds[id]);
        if(activityDescription.isEmpty()){
            activityDescription=getString(R.string.noDescriptionAvailableText);
        } else {
            activityDescription = activityDescription.replaceAll("\\*", "\n");
        }

        activityDescription+="\n";

        descriptionText.setText(activityDescription);

        dialog.show();
    }

    public void chooseItem(int id, View v) {
        TextView tv = (TextView)v;
        tv.setBackgroundColor(ContextCompat.getColor(this,R.color.colorPrimaryDark));
        final Context context = this;
        Intent i = new Intent(context, chooseFieldPlot.class);
        i.putExtra("userId", userId);
        i.putExtra("userRole", userRole);
        i.putExtra("task", "measurement");
        i.putExtra("field", -1);
        i.putExtra("measurement", measurementIds[id]);
        i.putExtra("title",agroHelper.getMeasurementNameFromId(measurementIds[id])+" ("+agroHelper.getMeasurementCategoryFromId(measurementIds[id])+")");
        i.putExtra("measurementChosenCategory",chosenCategory);
        startActivity(i);
        finish();
    }
}
