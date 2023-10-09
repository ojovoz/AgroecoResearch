package ojovoz.agroecoresearch;

import android.app.AlertDialog;
import android.app.Dialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.renderscript.ScriptGroup;
import android.support.v4.content.ContextCompat;
import android.support.v7.app.AppCompatActivity;
import android.text.Editable;
import android.text.InputType;
import android.text.TextWatcher;
import android.util.TypedValue;
import android.view.Gravity;
import android.view.MenuItem;
import android.view.MotionEvent;
import android.view.View;
import android.view.Window;
import android.view.inputmethod.EditorInfo;
import android.view.inputmethod.InputMethodManager;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.ListAdapter;
import android.widget.ScrollView;
import android.widget.TableLayout;
import android.widget.TableRow;
import android.widget.TextView;
import android.widget.Toast;

import com.google.android.gms.common.api.GoogleApiClient;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.Iterator;
import java.util.TimeZone;

/**
 * Created by Eugenio on 21/04/2017.
 */
public class enterMeasurement extends AppCompatActivity {

    public int userId;
    public int userRole;
    public String task;
    public int logId;
    public int fieldId;
    public String plots;
    public int measurementId;
    public boolean hasSamples;
    public String measurementTitle;
    public String shortTitle;
    public String measurementChosenCategory;
    public String measurementUnits;
    public int type;
    public float min;
    public float max;
    ArrayList<CharSequence> categories;
    CharSequence categoriesArray[];
    public String categoriesString;
    public String measurementCategory = "";

    public Date measurementDate;

    public String update;

    public boolean changes=false;
    public int exitAction;

    public ArrayList<oSampleHelper> samples;
    public int maxSampleNumber = 1;

    public boolean bDeleting=false;

    public Button currentSampleChoiceButton=null;
    private promptDialog dlg=null;
    /**
     * ATTENTION: This was auto-generated to implement the App Indexing API.
     * See https://g.co/AppIndexing/AndroidStudio for more information.
     */
    private GoogleApiClient client;

    @Override
    public void onCreate(Bundle savedInstanceState) {

        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_enter_measurement);

        userId = getIntent().getExtras().getInt("userId");
        userRole = getIntent().getExtras().getInt("userRole");
        task = getIntent().getExtras().getString("task");
        fieldId = getIntent().getExtras().getInt("field");
        plots = getIntent().getExtras().getString("plots");
        measurementId = getIntent().getExtras().getInt("measurement");
        hasSamples = getIntent().getExtras().getBoolean("hasSamples");
        measurementTitle = getIntent().getExtras().getString("title");
        shortTitle = getIntent().getExtras().getString("shortTitle");
        measurementChosenCategory = getIntent().getExtras().getString("measurementChosenCategory");
        measurementUnits = getIntent().getExtras().getString("units");
        type = getIntent().getExtras().getInt("type");
        min = getIntent().getExtras().getFloat("min");
        max = getIntent().getExtras().getFloat("max");
        categoriesString = getIntent().getExtras().getString("categories");
        update = getIntent().getExtras().getString("update");

        categories = new ArrayList<>();
        String[] categoriesParts = categoriesString.split(",");
        for (int i = 0; i < categoriesParts.length; i++) {
            categories.add(categoriesParts[i]);
        }
        categories.add(getString(R.string.otherListText));
        categoriesArray = categories.toArray(new CharSequence[categories.size()]);

        TextView tt = (TextView) findViewById(R.id.fieldPlotText);
        tt.setText(measurementTitle);

        EditText tOther = (EditText) findViewById(R.id.measurementOtherTextValue);
        tOther.setVisibility(View.GONE);

        EditText ve = (EditText) findViewById(R.id.measurementValue);
        EditText mc = (EditText) findViewById(R.id.measurementComments);

        if (hasSamples) {
            TextView vt = (TextView) findViewById(R.id.enterValueText);
            vt.setVisibility(View.GONE);
            ve.setVisibility(View.GONE);

            Button cb = (Button) findViewById(R.id.measurementCategory);
            cb.setVisibility(View.GONE);

            if(!measurementUnits.isEmpty()) {
                TextView tvs = (TextView) findViewById(R.id.enterSamplesText);
                tvs.setText(tvs.getText() + " (" + measurementUnits + ")");
            }
        } else {
            TableLayout tl = (TableLayout) findViewById(R.id.samples);
            tl.setVisibility(View.GONE);

            Button b = (Button) findViewById(R.id.addSample);
            b.setVisibility(View.GONE);

            TextView tvs = (TextView)findViewById(R.id.enterSamplesText);
            tvs.setVisibility(View.GONE);

            if (type == 1 && !measurementUnits.equals("date")) {
                Button cb = (Button) findViewById(R.id.measurementCategory);
                cb.setVisibility(View.GONE);

                TextView tv = (TextView)findViewById(R.id.enterValueText);
                tv.setText(tv.getText()+" ("+ measurementUnits +")");

            } else if (type == 0 && !measurementUnits.equals("date")) {
                TextView vt = (TextView) findViewById(R.id.enterValueText);
                vt.setVisibility(View.GONE);
                ve.setVisibility(View.GONE);

                Button cb = (Button) findViewById(R.id.measurementCategory);
                cb.setOnClickListener(new View.OnClickListener() {
                    @Override
                    public void onClick(View v) {
                        switch (v.getId()) {
                            case R.id.measurementCategory:
                                showMeasurementCategories();
                                break;
                            default:
                                break;
                        }
                    }
                });
            } else if (type == 2){
                /*
                TextView vt = (TextView) findViewById(R.id.enterValueText);
                vt.setVisibility(View.GONE);
                ve.setVisibility(View.GONE);

                Button cb = (Button) findViewById(R.id.measurementCategory);
                cb.setOnClickListener(new View.OnClickListener() {
                    @Override
                    public void onClick(View v) {
                        switch (v.getId()) {
                            case R.id.measurementCategory:
                                showHealthReport();
                                break;
                            default:
                                break;
                        }
                    }
                });
                cb.setText(R.string.healthReportButtonText);
                */
            } else if (measurementUnits.equals("date")) {
                Button cb = (Button) findViewById(R.id.measurementCategory);
                cb.setVisibility(View.GONE);
                TextView vt = (TextView) findViewById(R.id.enterValueText);
                vt.setVisibility(View.GONE);
                ve.setVisibility(View.GONE);

            }
        }

        String healthReportValues = getIntent().getExtras().getString("healthReportValues");
        int sampleId = getIntent().getExtras().getInt("sampleId");
        if(healthReportValues==null){
            healthReportValues="";
            sampleId=-1;
        }

        if (update.equals("measurement")) {
            logId = getIntent().getExtras().getInt("logId");

            if(hasSamples){
                initializeSampleTable(getIntent().getExtras().getString("measurementCategory"),healthReportValues,sampleId);

                Button as = (Button) findViewById(R.id.addSample);
                as.setOnClickListener(new View.OnClickListener() {
                    @Override
                    public void onClick(View v) {
                        addSample();
                    }
                });

                TableLayout st = (TableLayout)findViewById(R.id.samplesTable);
                st.setOnTouchListener(new View.OnTouchListener() {
                    @Override
                    public boolean onTouch(View view, MotionEvent motionEvent) {
                        findViewById(R.id.childScrollView).getParent().requestDisallowInterceptTouchEvent(true);
                        return false;
                    }
                });

                ScrollView svp = (ScrollView) findViewById(R.id.parentScrollView);
                svp.setOnTouchListener(new View.OnTouchListener() {
                    @Override
                    public boolean onTouch(View view, MotionEvent motionEvent) {
                        findViewById(R.id.childScrollView).getParent().requestDisallowInterceptTouchEvent(false);
                        return false;
                    }
                });
            } else if (type == 1 && !measurementUnits.equals("date")) {
                ve.setText(String.valueOf(getIntent().getExtras().getFloat("measurementValue")));
            } else if (type == 0 && !measurementUnits.equals("date")) {
                Button cb = (Button) findViewById(R.id.measurementCategory);
                measurementCategory = getIntent().getExtras().getString("measurementCategory");
                if (!categories.contains(measurementCategory)) {
                    tOther.setVisibility(View.VISIBLE);
                    tOther.setText(measurementCategory);
                    cb.setText(getString(R.string.otherButtonText));
                } else {
                    cb.setText(getIntent().getExtras().getString("measurementCategory"));
                }
            }

            Button db = (Button) findViewById(R.id.dateButton);
            db.setText(getIntent().getExtras().getString("measurementDate"));
            measurementDate = stringToDate(getIntent().getExtras().getString("measurementDate"));

            mc.setText(getIntent().getExtras().getString("measurementComments"));

            Button rb = (Button) findViewById(R.id.okButton);
            rb.setText(R.string.editButtonText);
        } else {
            String date = getIntent().getExtras().getString("measurementDate");
            if(date!=null){
                measurementDate=stringToDate(date);
            } else {
                measurementDate = new Date();
            }

            if (hasSamples) {
                if(sampleId>=0) {
                    String samples = getIntent().getExtras().getString("measurementCategory");
                    initializeSampleTable(samples,healthReportValues,sampleId);
                } else {
                    initializeSampleTable("",healthReportValues,sampleId);
                }
                Button as = (Button) findViewById(R.id.addSample);
                as.setOnClickListener(new View.OnClickListener() {
                    @Override
                    public void onClick(View v) {
                        addSample();
                    }
                });

                TableLayout st = (TableLayout)findViewById(R.id.samplesTable);
                st.setOnTouchListener(new View.OnTouchListener() {
                    @Override
                    public boolean onTouch(View view, MotionEvent motionEvent) {
                        findViewById(R.id.childScrollView).getParent().requestDisallowInterceptTouchEvent(true);
                        return false;
                    }
                });

                ScrollView svp = (ScrollView) findViewById(R.id.parentScrollView);
                svp.setOnTouchListener(new View.OnTouchListener() {
                    @Override
                    public boolean onTouch(View view, MotionEvent motionEvent) {
                        findViewById(R.id.childScrollView).getParent().requestDisallowInterceptTouchEvent(false);
                        return false;
                    }
                });

            }

        }

        mc.addTextChangedListener(new TextWatcher() {
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

        ve.addTextChangedListener(new TextWatcher() {
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

        Button cb = (Button) findViewById(R.id.dateButton);
        cb.setText(dateToString(measurementDate));
        cb.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
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
        if (update.equals("")) {
            Intent i = new Intent(context, chooseFieldPlot.class);
            i.putExtra("userId", userId);
            i.putExtra("userRole", userRole);
            i.putExtra("task", task);
            i.putExtra("field", fieldId);
            i.putExtra("plots", plots);
            i.putExtra("newMeasurement", false);
            i.putExtra("measurement", measurementId);
            i.putExtra("title", shortTitle);
            i.putExtra("measurementChosenCategory", measurementChosenCategory);
            startActivity(i);
            finish();
        } else {
            Intent i = new Intent(context, manageData.class);
            i.putExtra("userId", userId);
            i.putExtra("userRole", userRole);
            i.putExtra("update", "");
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

    public void initializeSampleTable(String ini, String healthReport, int updateSample) {
        oSampleHelper sh;
        samples = new ArrayList<>();
        if(ini.isEmpty()) {
            sh = new oSampleHelper();
            sh.sampleNumber = maxSampleNumber;
            sh.value = "";
            samples.add(sh);
        } else {
            String[] sampleItems = ini.split("\\*");
            for(int i=0;i<sampleItems.length;i+=2){
                sh = new oSampleHelper();
                sh.sampleNumber = Integer.valueOf(sampleItems[i]);
                sh.value = sampleItems[i+1];
                samples.add(sh);
            }
        }

        if(updateSample>=0){
            sh = samples.get(updateSample);
            sh.value=healthReport;
            samples.set(updateSample,sh);
            changes=true;
        }

        fillSampleTable();

    }

    public void fillSampleTable() {
        int n = 0;
        TableLayout samplesTable = (TableLayout) findViewById(R.id.samplesTable);
        samplesTable.removeAllViews();
        Iterator<oSampleHelper> iterator = samples.iterator();
        while (iterator.hasNext()) {
            oSampleHelper sh = iterator.next();

            final TableRow trow = new TableRow(enterMeasurement.this);
            TableRow.LayoutParams lp = new TableRow.LayoutParams(TableRow.LayoutParams.MATCH_PARENT, TableRow.LayoutParams.MATCH_PARENT, 1.0f);
            lp.setMargins(10, 10, 0, 10);
            if (n % 2 == 0) {
                trow.setBackgroundColor(ContextCompat.getColor(this, R.color.lightGray));
            } else {
                trow.setBackgroundColor(ContextCompat.getColor(this, R.color.colorWhite));
            }

            CheckBox cb = new CheckBox(enterMeasurement.this);
            cb.setButtonDrawable(R.drawable.delete_checkbox);
            cb.setId(n);
            cb.setPadding(10, 10, 10, 10);
            cb.setOnTouchListener(new View.OnTouchListener() {
                @Override
                public boolean onTouch(View view, MotionEvent motionEvent) {
                    findViewById(R.id.childScrollView).getParent().requestDisallowInterceptTouchEvent(true);
                    return false;
                }
            });

            cb.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(final View view) {
                    view.requestFocus();
                    bDeleting=true;
                    view.postDelayed(new Runnable() {
                        @Override
                        public void run() {
                            deleteSample(view);
                        }
                    },200);
                }
            });
            trow.addView(cb, lp);

            EditText sn = new EditText(enterMeasurement.this);
            sn.setTextSize(TypedValue.COMPLEX_UNIT_DIP, 17f);
            sn.setText(String.valueOf(sh.sampleNumber));
            sn.setTextAlignment(View.TEXT_ALIGNMENT_CENTER);
            sn.setInputType(InputType.TYPE_CLASS_NUMBER);
            sn.setImeOptions(EditorInfo.IME_ACTION_NEXT);
            sn.setPadding(0, 10, 0, 10);
            sn.setId(n);
            sn.setOnFocusChangeListener(new View.OnFocusChangeListener() {
                @Override
                public void onFocusChange(View view, boolean b) {
                    if (!b) {
                        updateSampleNumber(view);
                    }
                }
            });
            sn.setOnTouchListener(new View.OnTouchListener() {
                @Override
                public boolean onTouch(View view, MotionEvent motionEvent) {
                    findViewById(R.id.childScrollView).getParent().requestDisallowInterceptTouchEvent(true);
                    return false;
                }
            });
            sn.addTextChangedListener(new TextWatcher() {
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
            trow.addView(sn, lp);

            if (type == 0 || type == 2) {
                Button sb = new Button(enterMeasurement.this);
                sb.setTextSize(TypedValue.COMPLEX_UNIT_DIP, 17f);
                if(!sh.value.trim().isEmpty()) {
                    if(type==0) {
                        String display = sh.value;
                        if (display.length() > 10) {
                            display = display.substring(0, 9) + "…";
                        }
                        sb.setText(display);
                    } else {
                        if(isSampleGood(sh.value)){
                            sb.setText(R.string.healthReportButtonDefaultText);
                        } else {
                            sb.setText(R.string.chooseValueSampleTable);
                        }
                    }
                } else {
                    if(type==0) {
                        sb.setText(R.string.chooseValueSampleTable);
                    } else {
                        sb.setText(R.string.healthReportButtonDefaultText);
                    }
                }
                sb.setTextAlignment(View.TEXT_ALIGNMENT_CENTER);
                sb.setPadding(0,10,0,10);
                sb.setId(n);
                sb.setBackgroundResource(R.drawable.button_background);
                sb.setTextColor(Color.WHITE);
                sb.setOnTouchListener(new View.OnTouchListener() {
                    @Override
                    public boolean onTouch(View view, MotionEvent motionEvent) {
                        findViewById(R.id.childScrollView).getParent().requestDisallowInterceptTouchEvent(true);
                        return false;
                    }
                });
                if(type==0) {
                    sb.setOnClickListener(new View.OnClickListener() {
                        @Override
                        public void onClick(View v) {
                            currentSampleChoiceButton = (Button) v;
                            showMeasurementCategoriesSampleTable();
                            changes = true;
                        }
                    });
                } else {
                    sb.setOnClickListener(new View.OnClickListener() {
                        @Override
                        public void onClick(View v) {
                            currentSampleChoiceButton = (Button) v;
                            showHealthReportSampleTable(currentSampleChoiceButton.getId());
                            changes = true;
                        }
                    });
                }
                trow.addView(sb, lp);

            } else {
                EditText sv = new EditText(enterMeasurement.this);
                sv.setTextSize(TypedValue.COMPLEX_UNIT_DIP, 17f);
                sv.setText(sh.value);
                sv.setTextAlignment(View.TEXT_ALIGNMENT_CENTER);
                sv.setPadding(0, 10, 0, 10);
                sv.setRawInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
                sv.setImeOptions(EditorInfo.IME_ACTION_NEXT);
                sv.setId(n);
                sv.setOnFocusChangeListener(new View.OnFocusChangeListener() {
                    @Override
                    public void onFocusChange(View view, boolean b) {
                        if (!b) {
                            updateSampleValue(view);
                        }
                    }
                });
                sv.setOnTouchListener(new View.OnTouchListener() {
                    @Override
                    public boolean onTouch(View view, MotionEvent motionEvent) {
                        findViewById(R.id.childScrollView).getParent().requestDisallowInterceptTouchEvent(true);
                        return false;
                    }
                });
                sv.addTextChangedListener(new TextWatcher() {
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
                trow.addView(sv, lp);
            }

            trow.setGravity(Gravity.CENTER_VERTICAL);
            samplesTable.addView(trow, lp);
            n++;
        }
    }

    public void updateSampleValue(View view) {
        if(!bDeleting) {
            EditText e = (EditText) view;
            int id = e.getId();
            String value = String.valueOf(e.getText());

            if (!value.isEmpty()) {
                oSampleHelper sh = samples.get(id);
                sh.value = value;

                if (!isNumeric(value) || (Float.parseFloat(value) < min || Float.parseFloat(value) > max)) {
                    String msg = this.getResources().getString(R.string.sampleOutOfRange);
                    msg = msg.replaceAll("x", String.valueOf(sh.sampleNumber));
                    Toast.makeText(this, msg, Toast.LENGTH_SHORT).show();
                }
            }
        } else {
            bDeleting=false;
        }
    }

    public void updateSampleNumber(View view) {
        if(!bDeleting) {
            EditText e = (EditText) view;
            int id = e.getId();
            String sampleValue = String.valueOf(e.getText());
            if (!sampleValue.isEmpty()) {
                int number = Integer.valueOf(sampleValue);

                oSampleHelper sh = samples.get(id);
                sh.sampleNumber = number;
                int repeated = findRepeatedSampleNumber(number, id);
                if (repeated >= 0) {
                    String msg = this.getResources().getString(R.string.sampleNumberIsRepeated);
                    msg = msg.replaceAll("x", String.valueOf(sh.sampleNumber));
                    Toast.makeText(this, msg, Toast.LENGTH_SHORT).show();
                }
            }
        } else {
            bDeleting=false;
        }
    }

    public int findRepeatedSampleNumber(int number, int id){
        int ret=-1;
        Iterator<oSampleHelper> iterator = samples.iterator();
        int n=0;
        while (iterator.hasNext()) {
            oSampleHelper sh = iterator.next();
            if(n!=id){
                if(sh.sampleNumber==number){
                    ret=number;
                    break;
                }
            }
            n++;
        }
        return ret;
    }

    public int getMaxSampleNumber(){
        int ret=0;
        Iterator<oSampleHelper> iterator = samples.iterator();
        while (iterator.hasNext()) {
            oSampleHelper sh = iterator.next();
            if(sh.sampleNumber>ret){
                ret=sh.sampleNumber;
            }
        }
        return ret+1;
    }

    public void addSample() {
        maxSampleNumber=getMaxSampleNumber();
        oSampleHelper sh = new oSampleHelper();
        sh.sampleNumber = maxSampleNumber;
        sh.value = "";
        samples.add(sh);

        changes=true;

        fillSampleTable();

        final ScrollView sv = (ScrollView)findViewById(R.id.childScrollView);
        sv.postDelayed(new Runnable() { @Override public void run() { sv.fullScroll(View.FOCUS_DOWN); } }, 500);
    }

    public void deleteSample(View v){

        InputMethodManager imm = (InputMethodManager)getSystemService(Context.INPUT_METHOD_SERVICE);
        imm.hideSoftInputFromWindow(v.getWindowToken(), 0);

        final CheckBox c = (CheckBox)v;
        c.setChecked(true);
        final int deleteId = c.getId();
        oSampleHelper sh = samples.get(deleteId);

        String msg = this.getResources().getString(R.string.deleteSampleString);
        msg = msg.replaceAll("x", String.valueOf(sh.sampleNumber));

        AlertDialog.Builder logoutDialog = new AlertDialog.Builder(this);
        logoutDialog.setTitle(R.string.deleteSampleTitle);
        logoutDialog.setMessage(msg);
        logoutDialog.setNegativeButton(R.string.cancelButtonText, new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialogInterface, int i) {
                c.setChecked(false);
                bDeleting=false;
            }
        });
        logoutDialog.setPositiveButton(R.string.okButtonText, new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialogInterface, int i) {
                c.setChecked(false);
                changes=true;
                doDelete(deleteId);
            }
        });
        logoutDialog.create();
        logoutDialog.show();
    }

    public void doDelete(int id){
        samples.remove(id);
        fillSampleTable();
    }

    public boolean isSampleGood(String value){
        boolean ret=true;
        String[] healthValues;
        healthValues = value.split("\\#");
        for(int i=0;i<healthValues.length;i++){
            if(!healthValues[i].trim().isEmpty()){
                ret=false;
                break;
            }
        }
        return ret;
    }

    public boolean verifySamples(){
        boolean ret=true;
        int n=0;
        Iterator<oSampleHelper> iterator = samples.iterator();
        while (iterator.hasNext()) {
            oSampleHelper sh = iterator.next();
            if(findRepeatedSampleNumber(sh.sampleNumber,n)>0){
                String msg = this.getResources().getString(R.string.sampleNumberIsRepeated);
                msg = msg.replaceAll("x", String.valueOf(sh.sampleNumber));
                Toast.makeText(this, msg, Toast.LENGTH_SHORT).show();
                ret=false;
                break;
            } else {
                if (type==1 && (!isNumeric(sh.value) || (Float.parseFloat(sh.value) < min || Float.parseFloat(sh.value) > max))) {
                    String msg = this.getResources().getString(R.string.sampleOutOfRange);
                    msg = msg.replaceAll("x", String.valueOf(sh.sampleNumber));
                    Toast.makeText(this, msg, Toast.LENGTH_SHORT).show();
                    ret=false;
                    break;
                } else if(type==0 && sh.value.trim().isEmpty()){
                    String msg = this.getResources().getString(R.string.enterValidCategoryTextSample);
                    msg = msg.replaceAll("x", String.valueOf(sh.sampleNumber));
                    Toast.makeText(this, msg, Toast.LENGTH_SHORT).show();
                    ret=false;
                    break;
                }
            }
            n++;
        }
        return ret;
    }

    public String convertSamplesToString(){
        String ret="";
        Iterator<oSampleHelper> iterator = samples.iterator();
        while (iterator.hasNext()) {
            oSampleHelper sh = iterator.next();
            String value = sh.value;
            if(value.isEmpty()) { value=" "; }
            if(ret.isEmpty()){
                ret=String.valueOf(sh.sampleNumber)+"*"+value;
            } else {
                ret=ret+"*"+String.valueOf(sh.sampleNumber)+"*"+value;
            }
        }
        return ret;
    }

    public void showMeasurementCategoriesSampleTable(){

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
                if (i >= 0 && currentSampleChoiceButton!=null) {

                    String sampleMeasurementCategory = (String) categoriesArray[i];
                    if (sampleMeasurementCategory.equals(getString(R.string.otherListText))) {
                        oSampleHelper sh = samples.get(currentSampleChoiceButton.getId());
                        dlg = new promptDialog(enterMeasurement.this, R.string.emptyString, R.string.enterOtherValueLabel, sh.value) {
                            @Override
                            public boolean onOkClicked(String input) {
                                String display="";
                                input = input.replaceAll(";","");
                                input = input.replaceAll("\\|","");
                                input = input.replaceAll("\\*","");
                                if(input.length()>10){
                                    display=input.substring(0,9)+" …";
                                } else {
                                    display=input;
                                }
                                currentSampleChoiceButton.setText(display);
                                oSampleHelper sh = samples.get(currentSampleChoiceButton.getId());
                                sh.value=input;
                                return true;
                            }
                        };
                        dlg.show();
                    } else {
                        String display="";
                        String chosen=(String)categoriesArray[i];
                        if(chosen.length()>10){
                            display=chosen.substring(0,9)+" …";
                        } else {
                            display=chosen;
                        }
                        currentSampleChoiceButton.setText(display);
                        oSampleHelper sh = samples.get(currentSampleChoiceButton.getId());
                        sh.value=(String)categoriesArray[i];
                    }

                }
                dialogInterface.dismiss();

            }
        });
        AlertDialog dialog = builder.create();
        dialog.show();
    }

    public void showMeasurementCategories() {
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
                    Button fieldListView = (Button) findViewById(R.id.measurementCategory);
                    measurementCategory = (String) categoriesArray[i];
                    if (measurementCategory.equals(getString(R.string.otherListText))) {
                        fieldListView.setText(R.string.otherButtonText);
                        EditText tOther = (EditText) findViewById(R.id.measurementOtherTextValue);
                        tOther.setVisibility(View.VISIBLE);
                    } else {
                        fieldListView.setText(categoriesArray[i]);
                        EditText tOther = (EditText) findViewById(R.id.measurementOtherTextValue);
                        tOther.setVisibility(View.GONE);
                    }

                }
                dialogInterface.dismiss();
                changes=true;

            }
        });
        AlertDialog dialog = builder.create();
        dialog.show();
    }

    /*
    public void showHealthReport(){
        float valueNumber=0.0f;

        EditText c = (EditText)findViewById(R.id.measurementComments);
        String commentsText = String.valueOf(c.getText());
        if (!commentsText.isEmpty()) {
            commentsText = commentsText.replaceAll(";", " ");
            commentsText = commentsText.replaceAll("\\|", " ");
        }

        EditText value = (EditText) findViewById(R.id.measurementValue);
        String valueText = String.valueOf(value.getText());
        if (isNumeric(valueText)) {
            valueNumber = Float.parseFloat(valueText);
        }

        final Context context = this;
        Intent i = new Intent(context, enterHealthReport.class);
        i.putExtra("userId", userId);
        i.putExtra("userRole", userRole);
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
        i.putExtra("measurementDate", dateToString(measurementDate));
        i.putExtra("measurementValue", valueNumber);
        i.putExtra("units", measurementUnits);
        i.putExtra("measurementCategory", measurementCategory);
        i.putExtra("measurementComments", commentsText);
        i.putExtra("update",update);
        startActivity(i);
        finish();
    }
    */

    public void showHealthReportSampleTable(int id){

        int sampleNumber = samples.get(id).sampleNumber;
        String sampleValue = samples.get(id).value;

        EditText c = (EditText)findViewById(R.id.measurementComments);
        String commentsText = String.valueOf(c.getText());
        if (!commentsText.isEmpty()) {
            commentsText = commentsText.replaceAll(";", " ");
            commentsText = commentsText.replaceAll("\\|", " ");
        }

        String samplesString=convertSamplesToString();

        final Context context = this;
        Intent i = new Intent(context, enterHealthReport.class);
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
        i.putExtra("hasSamples",hasSamples);
        i.putExtra("type",type);
        i.putExtra("min",min);
        i.putExtra("max",max);
        i.putExtra("categories",categoriesString);
        i.putExtra("measurementDate", dateToString(measurementDate));
        i.putExtra("measurementCategory", samplesString);
        i.putExtra("units", measurementUnits);
        i.putExtra("measurementComments", commentsText);
        i.putExtra("update",update);
        i.putExtra("sampleNumber", sampleNumber);
        i.putExtra("sampleId",id);
        i.putExtra("previousValue",sampleValue);
        startActivity(i);
        finish();
    }

    public void displayDatePicker() {
        final Dialog dialog = new Dialog(this);
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialog.setContentView(R.layout.dialog_datepicker);

        DatePicker dp = (DatePicker) dialog.findViewById(R.id.datePicker);
        Calendar calActivity = Calendar.getInstance();
        calActivity.setTime(measurementDate);
        dp.init(calActivity.get(Calendar.YEAR), calActivity.get(Calendar.MONTH), calActivity.get(Calendar.DAY_OF_MONTH), null);

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
                int year = dp.getYear();
                Calendar calendar = Calendar.getInstance();
                calendar.set(year, month, day);

                measurementDate = calendar.getTime();

                Button cb = (Button) findViewById(R.id.dateButton);
                cb.setText(dateToString(measurementDate));
                dialog.dismiss();
                changes=true;
            }
        });
        dialog.show();
    }

    public Date stringToDate(String d) {
        Date date = new Date();
        SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd");
        sdf.setTimeZone(TimeZone.getDefault());
        try {
            date = sdf.parse(d);
        } catch (ParseException e) {

        }
        return date;
    }

    public boolean isNumeric(String str) {
        return str.matches("-?\\d+(\\.\\d+)?");
    }

    public String dateToString(Date d) {
        SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd");
        sdf.setTimeZone(TimeZone.getDefault());
        return sdf.format(d);
    }

    public void registerMeasurement(View v) {
        float valueNumber = 0.0f;
        String units = "";
        String samplesString = "";
        boolean bProceed = true;

        EditText c = (EditText)findViewById(R.id.measurementComments);
        c.requestFocus();
        String commentsText = String.valueOf(c.getText());

        if(hasSamples){
            if(verifySamples()){
                samplesString=convertSamplesToString();
            } else {
                bProceed=false;
            }
        } else {

            if (type == 1 && !measurementUnits.equals("date")) {
                EditText value = (EditText) findViewById(R.id.measurementValue);
                String valueText = String.valueOf(value.getText());
                if (isNumeric(valueText)) {
                    valueNumber = Float.parseFloat(valueText);
                    if (valueNumber < min || valueNumber > max) {
                        Toast.makeText(this, R.string.valueOutOfRangeText, Toast.LENGTH_SHORT).show();
                        value.requestFocus();
                        bProceed = false;
                    }
                } else {
                    Toast.makeText(this, R.string.enterValidNumberText, Toast.LENGTH_SHORT).show();
                    value.requestFocus();
                    bProceed = false;
                }
            } else if (type == 0 && !measurementUnits.equals("date")) {
                if (measurementCategory.equals("")) {
                    Toast.makeText(this, R.string.enterValidCategoryText, Toast.LENGTH_SHORT).show();
                    bProceed = false;
                }
            }

            if (measurementCategory.equals(getString(R.string.otherListText))) {
                EditText tOther = (EditText) findViewById(R.id.measurementOtherTextValue);
                measurementCategory = String.valueOf(tOther.getText());
                if (!measurementCategory.isEmpty()) {
                    measurementCategory = measurementCategory.replaceAll(";", " ");
                    measurementCategory = measurementCategory.replaceAll("\\|", " ");
                    measurementCategory = measurementCategory.replaceAll("\\*", " ");
                }
            }
        }

        if (!commentsText.isEmpty()) {
            commentsText = commentsText.replaceAll(";", " ");
            commentsText = commentsText.replaceAll("\\|", " ");
        }

        if (update.equals("") && bProceed) {
            Toast.makeText(this, "Measurement saved successfully", Toast.LENGTH_SHORT).show();
            Intent i = new Intent(this, chooseFieldPlot.class);
            i.putExtra("userId", userId);
            i.putExtra("userRole", userRole);
            i.putExtra("task", task);
            i.putExtra("title", shortTitle);
            i.putExtra("measurementChosenCategory", measurementChosenCategory);
            i.putExtra("field", fieldId);
            i.putExtra("plots", plots);
            i.putExtra("newMeasurement", true);
            i.putExtra("measurement", measurementId);
            i.putExtra("measurementDate", dateToString(measurementDate));
            i.putExtra("measurementValue", valueNumber);
            i.putExtra("measurementUnits", measurementUnits);
            if(hasSamples) {
                i.putExtra("measurementCategory", samplesString);
            } else {
                i.putExtra("measurementCategory", measurementCategory);
            }
            i.putExtra("measurementComments", commentsText);
            startActivity(i);
            finish();
        } else if (bProceed) {
            Toast.makeText(this, "Measurement edited successfully", Toast.LENGTH_SHORT).show();
            Intent i = new Intent(this, manageData.class);
            i.putExtra("userId", userId);
            i.putExtra("userRole", userRole);
            i.putExtra("task", task);
            i.putExtra("logId", logId);
            i.putExtra("update", "measurement");
            i.putExtra("measurement", measurementId);
            i.putExtra("measurementDate", dateToString(measurementDate));
            i.putExtra("measurementValue", valueNumber);
            i.putExtra("measurementUnits", measurementUnits);
            if(hasSamples){
                i.putExtra("measurementCategory", samplesString);
            } else {
                i.putExtra("measurementCategory", measurementCategory);
            }
            i.putExtra("measurementComments", commentsText);
            startActivity(i);
            finish();
        }
    }

}
