package ojovoz.agroecoresearch;

import android.app.Dialog;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.support.v4.content.ContextCompat;
import android.support.v7.app.AppCompatActivity;
import android.util.TypedValue;
import android.view.Gravity;
import android.view.MenuItem;
import android.view.View;
import android.view.Window;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.TableLayout;
import android.widget.TableRow;
import android.widget.TextView;
import android.widget.Toast;

import java.util.ArrayList;
import java.util.Iterator;

/**
 * Created by Eugenio on 05/04/2017.
 */
public class chooser extends AppCompatActivity {

    public int userId;
    public int userRole;
    public String task;
    public int fieldId;

    public oField field;

    public ArrayList<oActivity> activities;

    public agroecoHelper agroHelper;

    @Override
    public void onCreate(Bundle savedInstanceState) {

        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_chooser);


        userId = getIntent().getExtras().getInt("userId");
        userRole = getIntent().getExtras().getInt("userRole");
        task = getIntent().getExtras().getString("task");

        agroHelper = new agroecoHelper(this,"crops,fields,treatments,activities");

        if(getIntent().getExtras().getBoolean("newActivity")){
            String plots = getIntent().getExtras().getString("plots");
            agroHelper.addActivityToLog(getIntent().getExtras().getInt("field"), plots, userId, getIntent().getExtras().getInt("activity"),
                    getIntent().getExtras().getString("activityDate"), getIntent().getExtras().getFloat("activityValue"),
                    getIntent().getExtras().getString("activityUnits"), getIntent().getExtras().getString("activityLaborers"),
                    getIntent().getExtras().getString("activityCost"), getIntent().getExtras().getString("activityComments"),
                    getIntent().getExtras().getBoolean("copy"));
        }

        setTitle("Enter "+task);

        TextView tt = (TextView)findViewById(R.id.tableTitle);
        tt.setText("Choose "+task+":");

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

    @Override public void onBackPressed(){
        final Context context = this;
        Intent i = new Intent(context, mainMenu.class);
        i.putExtra("userId",userId);
        i.putExtra("userRole",userRole);
        i.putExtra("task",task);
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

    public void fillTable(){
        TableLayout chooserTable = (TableLayout) findViewById(R.id.chooserTable);
        chooserTable.removeAllViews();
        String category="";
        if(task.equals("activity")){
            activities = agroHelper.getActivities(null,null);
            Iterator<oActivity> iterator = activities.iterator();
            int n=0;
            while (iterator.hasNext()) {
                oActivity activity = iterator.next();

                if(!activity.activityCategory.equals(category)){
                    final TableRow trow = new TableRow(chooser.this);
                    TableRow.LayoutParams lp = new TableRow.LayoutParams(TableRow.LayoutParams.MATCH_PARENT, TableRow.LayoutParams.MATCH_PARENT, 1.0f);
                    lp.setMargins(4,4,4,4);
                    trow.setBackgroundColor(ContextCompat.getColor(this,R.color.colorPrimary));
                    TextView tv = new TextView(chooser.this);
                    tv.setTextColor(ContextCompat.getColor(this,R.color.colorWhite));
                    tv.setText(activity.activityCategory);
                    tv.setTextSize(TypedValue.COMPLEX_UNIT_DIP,20f);
                    tv.setPadding(4,4,4,4);
                    trow.addView(tv,lp);
                    trow.setGravity(Gravity.CENTER_VERTICAL);
                    chooserTable.addView(trow, lp);
                    category=activity.activityCategory;
                }

                final TableRow trow = new TableRow(chooser.this);
                TableRow.LayoutParams lpRow = new TableRow.LayoutParams(TableRow.LayoutParams.MATCH_PARENT, TableRow.LayoutParams.MATCH_PARENT, 1.0f);
                lpRow.setMargins(4, 4, 4, 4);
                TableRow.LayoutParams lpCb = new TableRow.LayoutParams(TableRow.LayoutParams.MATCH_PARENT, TableRow.LayoutParams.MATCH_PARENT, 0.2f);
                lpCb.setMargins(4, 4, 4, 4);
                TableRow.LayoutParams lpTv = new TableRow.LayoutParams(TableRow.LayoutParams.MATCH_PARENT, TableRow.LayoutParams.MATCH_PARENT, 0.8f);
                lpTv.setMargins(4, 4, 4, 4);

                if(n%2==0){
                    trow.setBackgroundColor(ContextCompat.getColor(this,R.color.lightGray));
                } else {
                    trow.setBackgroundColor(ContextCompat.getColor(this,R.color.colorWhite));
                }

                CheckBox cb = new CheckBox(chooser.this);
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

                TextView tv = new TextView(chooser.this);
                tv.setId(n);
                tv.setTextColor(ContextCompat.getColor(this,R.color.colorPrimary));
                tv.setText(activity.activityName);
                tv.setTextSize(TypedValue.COMPLEX_UNIT_DIP,20f);
                tv.setPadding(4,4,4,4);
                tv.setMaxWidth(350);
                tv.setOnClickListener(new View.OnClickListener() {

                        @Override
                        public void onClick(View v) {
                            chooseItem(v.getId(), v);
                        }

                    });
                trow.addView(tv,lpTv);
                trow.setGravity(Gravity.CENTER_VERTICAL);
                chooserTable.addView(trow, lpRow);

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
        descriptionTitle.setText("Activity: "+activities.get(id).activityName);

        TextView descriptionText = (TextView)dialog.findViewById(R.id.description_text);
        String activityDescription = activities.get(id).activityDescription;
        if(activityDescription.isEmpty()){
            activityDescription=getString(R.string.noDescriptionAvailableText);
        } else {
            activityDescription = activityDescription.replaceAll("\\*", "\n");
        }

        activityDescription+="\n";

        descriptionText.setText(activityDescription);

        dialog.show();
    }

    public void chooseItem(int id, View v){
        TextView tv = (TextView)v;
        tv.setBackgroundColor(ContextCompat.getColor(this,R.color.colorPrimaryDark));
        final Context context = this;

        Intent i = new Intent(context, chooseFieldPlot.class);
        i.putExtra("userId", userId);
        i.putExtra("userRole", userRole);
        i.putExtra("task", task);
        i.putExtra("field", -1);
        i.putExtra("activity", activities.get(id).activityId);
        i.putExtra("title",activities.get(id).activityName);


        startActivity(i);
        finish();

    }
}
