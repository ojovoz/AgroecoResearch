package ojovoz.agroecoresearch;

import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Color;
import android.graphics.drawable.GradientDrawable;
import android.os.Bundle;
import android.support.v4.content.ContextCompat;
import android.support.v7.app.AppCompatActivity;
import android.view.Gravity;
import android.view.MenuItem;
import android.view.View;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.ListAdapter;
import android.widget.TableLayout;
import android.widget.TableRow;
import android.widget.TextView;
import android.widget.Toast;

import java.util.ArrayList;
import java.util.Iterator;

/**
 * Created by Eugenio on 02/04/2017.
 */
public class chooseFieldPlot extends AppCompatActivity {

    public int userId;
    public int userRole;
    public String task;
    public String itemTitle;

    private preferenceManager prefs;

    public agroecoHelper agroHelper;

    ArrayList<oField> fields;
    CharSequence fieldsArray[];
    oField field;

    ArrayList<oPlotHelper> plotsInGrid;
    public View previousPlot=null;
    public int previousPlotN=-1;

    public String legend;
    public String measurementCategory;

    public int taskId;
    public String subTask="";

    ArrayList<oMeasuredPlotHelper> measuredPlots;

    @Override
    public void onCreate(Bundle savedInstanceState) {

        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_choose_field_plot);

        prefs = new preferenceManager(this);

        userId = getIntent().getExtras().getInt("userId");
        userRole = getIntent().getExtras().getInt("userRole");
        task = getIntent().getExtras().getString("task");
        String plots = getIntent().getExtras().getString("plots");
        if(task.equals("measurement")){
            measurementCategory = getIntent().getExtras().getString("measurementChosenCategory");
            taskId = getIntent().getExtras().getInt("measurement");
            getMeasuredPlots();
        } else if (task.equals("activity")){
            taskId = getIntent().getExtras().getInt("activity");
        } else if (task.equals("input")){
            if(getIntent().getExtras().getInt("cropId")>=0){
                taskId=getIntent().getExtras().getInt("cropId");
                subTask="crop";
            } else {
                taskId=getIntent().getExtras().getInt("treatmentId");
                subTask="treatment";
            }
        }
        itemTitle = getIntent().getExtras().getString("title");
        int fieldId = getIntent().getExtras().getInt("field");
        if(!(fieldId>=0)){
            if(!prefs.getPreference("field").isEmpty()) {
                fieldId = Integer.parseInt(prefs.getPreference("field"));
            }
        }

        if(!task.equals("activity")) {
            CharSequence title = getTitle() + ": " + itemTitle;
            setTitle(title);
        } else {
            CharSequence title = getTitle() + ": " + task;
            setTitle(title);
        }

        agroHelper = new agroecoHelper(this,"crops,fields,treatments,activities,measurements");
        fields = agroHelper.fields;
        fieldId = (agroHelper.fieldIdExists(fieldId)) ? fieldId : -1;

        if(getIntent().getExtras().getBoolean("newCropInput")){
            /*
            agroHelper.addCropToInputLog(fieldId, plots, userId, getIntent().getExtras().getInt("cropId"),
                    getIntent().getExtras().getString("cropInputDate"), getIntent().getExtras().getString("cropInputAge"),
                    getIntent().getExtras().getString("cropInputOrigin"), getIntent().getExtras().getFloat("cropInputQuantity"),
                    getIntent().getExtras().getString("cropInputCost"), getIntent().getExtras().getString("cropInputComments"));
                    */
        } else if(getIntent().getExtras().getBoolean("newTreatmentInput")){
            /*
            agroHelper.addTreatmentToInputLog(fieldId, plots, userId, getIntent().getExtras().getInt("treatmentId"),
                    getIntent().getExtras().getString("treatmentInputDate"), getIntent().getExtras().getString("treatmentInputMaterial"),
                    getIntent().getExtras().getFloat("treatmentInputQuantity"), getIntent().getExtras().getString("treatmentInputMethod"),
                    getIntent().getExtras().getString("treatmentInputCost"), getIntent().getExtras().getString("treatmentInputComments"));
                    */
        } else if(getIntent().getExtras().getBoolean("newActivity")){
            agroHelper.addActivityToLog(getIntent().getExtras().getInt("field"), plots, userId, getIntent().getExtras().getInt("activity"),
                    getIntent().getExtras().getString("activityDate"), getIntent().getExtras().getFloat("activityValue"),
                    getIntent().getExtras().getString("activityUnits"), getIntent().getExtras().getString("activityLaborers"),
                    getIntent().getExtras().getString("activityCost"), getIntent().getExtras().getString("activityComments"),
                    getIntent().getExtras().getBoolean("copy"));
            taskId=-1;
        } else if(getIntent().getExtras().getBoolean("newMeasurement")){
            agroHelper.addMeasurementToLog(fieldId, plots, userId, getIntent().getExtras().getInt("measurement"),
                    getIntent().getExtras().getString("measurementDate"), getIntent().getExtras().getFloat("measurementValue"),
                    getIntent().getExtras().getString("measurementUnits"), getIntent().getExtras().getString("measurementCategory"),
                    getIntent().getExtras().getString("measurementComments"));
            updateMeasuredPlots(fieldId,plots,getIntent().getExtras().getInt("measurement"));
        }


        ArrayList<CharSequence> tf = new ArrayList<>();
        for(int i=0; i<fields.size(); i++){
            tf.add(fields.get(i).fieldName + " replication " + Integer.toString(fields.get(i).fieldReplicationN));
        }
        fieldsArray=tf.toArray(new CharSequence[tf.size()]);

        Button fieldListView = (Button) findViewById(R.id.chooseFieldButton);

        fieldListView.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                switch (v.getId()) {
                    case R.id.chooseFieldButton:
                        showSelectFieldsDialog();
                        break;
                    default:
                        break;
                }
            }
        });

        if(fieldId>=0){
            field = agroHelper.getFieldFromId(fieldId);
            fieldListView = (Button) findViewById(R.id.chooseFieldButton);
            fieldListView.setText(field.fieldName + " r" + field.fieldReplicationN);
            String msg="";
            if(task.equals("measurement")){
                msg = getString(R.string.chooseSinglePlotPrompt);
            } else {
                msg = getString(R.string.choosePlotPrompt);
            }
            TextView choosePlotMessage = (TextView) findViewById(R.id.choosePlotMessage);
            choosePlotMessage.setText(msg);
            drawPlots();
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

    @Override public void onBackPressed(){
        final Context context = this;
        Intent i;
        if(task.equals("activity")) {
            i = new Intent(context, mainMenu.class);
        } else if(task.equals("measurement")){
            i = new Intent(context, measurementChooser.class);
            i.putExtra("measurementChosenCategory",measurementCategory);
        } else if(task.equals("input")){
            i = new Intent(context, inputChooser.class);
        } else {
            i = new Intent(context, mainMenu.class);
        }
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

    public void getMeasuredPlots(){
        measuredPlots = new ArrayList<>();
        String mp = prefs.getPreference("measuredPlots");
        if(!mp.isEmpty()){
            String mpList[] = mp.split(";");
            for(int i=0;i<mpList.length;i++){
                String mpElements[] = mpList[i].split(",");
                oMeasuredPlotHelper measuredPlot = new oMeasuredPlotHelper(Integer.valueOf(mpElements[0]),Integer.valueOf(mpElements[1]),Integer.valueOf(mpElements[2]));
                measuredPlots.add(measuredPlot);
            }
        }
    }

    public void updateMeasuredPlots(int f, String p, int m){
        int plot = Integer.valueOf(p);
        oMeasuredPlotHelper measuredPlot = new oMeasuredPlotHelper(f,plot,m);
        measuredPlots.add(measuredPlot);
        String mp = prefs.getPreference("measuredPlots");
        if(mp.isEmpty()){
            mp=String.valueOf(f)+","+p+","+String.valueOf(m);
        } else {
            mp=mp+";"+String.valueOf(f)+","+p+","+String.valueOf(m);
        }
        prefs.savePreference("measuredPlots",mp);
    }

    public boolean hasPlotBeenMeasured(int p){
        boolean ret=false;
        int f = field.fieldId;
        Iterator<oMeasuredPlotHelper> iterator = measuredPlots.iterator();
        while (iterator.hasNext()) {
            oMeasuredPlotHelper mp = iterator.next();
            if(mp.fieldId==f && mp.plotNumber==p && mp.measurementId==taskId){
                ret=true;
                break;
            }
        }
        return ret;
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

    public void showSelectFieldsDialog(){
        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        builder.setCancelable(true);
        builder.setNegativeButton(R.string.cancelButtonText, new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int which) {
                dialog.dismiss();
            }
        });
        final ListAdapter adapter = new ArrayAdapter<>(this,R.layout.checked_list_template,fieldsArray);
        builder.setSingleChoiceItems(adapter,-1,new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialogInterface, int i) {
                String msg="";
                if(i>=0) {
                    field = fields.get(i);
                    prefs.savePreference("field",Integer.toString(field.fieldId));
                    Button fieldListView = (Button) findViewById(R.id.chooseFieldButton);
                    fieldListView.setText(field.fieldName + " r" + field.fieldReplicationN);
                    if(task.equals("measurement")){
                        msg = getString(R.string.chooseSinglePlotPrompt);
                    } else {
                        msg = getString(R.string.choosePlotPrompt);
                    }
                    drawPlots();
                }
                TextView choosePlotMessage = (TextView) findViewById(R.id.choosePlotMessage);
                choosePlotMessage.setText(msg);
                dialogInterface.dismiss();

            }
        });
        AlertDialog dialog = builder.create();
        dialog.show();
    }

    void drawPlots(){

        ArrayList<oPlot> plots = field.plots;
        plotsInGrid = new ArrayList<>();

        TableLayout plotsGrid = (TableLayout) findViewById(R.id.plotsGrid);
        plotsGrid.removeAllViews();

        int n=0;
        int preChosenPlots=0;
        String cropsInLegend="";
        String intercropInLegend="";
        ArrayList<oCrop> cropList=new ArrayList<>();
        String[] treatmentNames = {"Control treatment","Soil management","Pest control","Soil management and pest control"};
        ArrayList<String> treatmentLegends = new ArrayList<>();
        for(int i=0;i<field.rows;i++){
            final TableRow trow = new TableRow(chooseFieldPlot.this);
            TableRow.LayoutParams lp = new TableRow.LayoutParams(TableRow.LayoutParams.MATCH_PARENT, TableRow.LayoutParams.MATCH_PARENT, 1.0f);
            lp.setMargins(2,2,2,2);
            for(int j=0;j<field.columns;j++){
                oPlot plot = plots.get(n);

                boolean isChooseable=agroHelper.isPlotChooseable(plot,task,subTask,taskId);
                boolean state;
                boolean hasBeenMeasured= (task.equals("measurement")) ? hasPlotBeenMeasured(n) : false;

                Button b = new Button(chooseFieldPlot.this);
                b.setId(n);
                b.setPadding(3,3,3,3);

                GradientDrawable drawable = new GradientDrawable();
                drawable.setShape(GradientDrawable.RECTANGLE);

                if(hasBeenMeasured){
                    drawable.setStroke(5, ContextCompat.getColor(this,R.color.colorBlack));
                    state=false;
                } else if((isChooseable && !task.equals("measurement")) || (isChooseable && task.equals("measurement") && preChosenPlots==0)) {
                    drawable.setStroke(5, Color.RED);
                    preChosenPlots++;
                    state=true;
                    if(task.equals("measurement")){
                        previousPlot=b;
                        previousPlotN=n;
                    }
                } else {
                    drawable.setStroke(5, Color.WHITE);
                    state=false;
                }

                if(!plot.hasPestControl && !plot.hasSoilManagement){
                    drawable.setColor(agroHelper.getTreatmentColor(1));
                    if(!treatmentLegends.contains(treatmentNames[0])){
                        treatmentLegends.add(treatmentNames[0]);
                    }
                } else if(!plot.hasPestControl && plot.hasSoilManagement){
                    drawable.setColor(agroHelper.getTreatmentColor(2));
                    if(!treatmentLegends.contains(treatmentNames[1])){
                        treatmentLegends.add(treatmentNames[1]);
                    }
                } else if(plot.hasPestControl && !plot.hasSoilManagement){
                    drawable.setColor(agroHelper.getTreatmentColor(3));
                    if(!treatmentLegends.contains(treatmentNames[2])){
                        treatmentLegends.add(treatmentNames[2]);
                    }
                } else {
                    drawable.setColor(agroHelper.getTreatmentColor(4));
                    if(!treatmentLegends.contains(treatmentNames[3])){
                        treatmentLegends.add(treatmentNames[3]);
                    }
                }

                b.setTextColor(ContextCompat.getColor(this, R.color.colorBlack));
                b.setBackground(drawable);

                oCrop pc = plot.primaryCrop;
                if(cropList.isEmpty()){
                    cropList.add(pc);
                } else {
                    if(!cropList.contains(pc)){
                        cropList.add(pc);
                    }
                }

                String cropsInPlot = pc.cropSymbol;

                oCrop ic = plot.intercroppingCrop;
                if(ic!=null){
                    cropsInPlot += "+L";
                    intercropInLegend="\nL: " + ic.cropName;
                }

                b.setText(cropsInPlot);
                if(isChooseable && !hasBeenMeasured) {
                    b.setOnClickListener(new View.OnClickListener() {

                        @Override
                        public void onClick(View v) {
                            choosePlot(v.getId(), v);
                        }

                    });
                }
                trow.addView(b,lp);

                oPlotHelper np = new oPlotHelper(plot,n,state,isChooseable);
                plotsInGrid.add(np);

                n++;
            }
            trow.setGravity(Gravity.CENTER_VERTICAL);
            plotsGrid.addView(trow, lp);
        }
        Button b = (Button)findViewById(R.id.enterDataButton);
        b.setVisibility(View.VISIBLE);

        cropList=agroHelper.sortCropListBySymbol(cropList);

        Iterator<oCrop> iteratorC = cropList.iterator();
        while (iteratorC.hasNext()){
            oCrop cl = iteratorC.next();
            if(cropsInLegend.isEmpty()){
                cropsInLegend=cl.cropSymbol + ": " + cl.cropName;
            } else {
                cropsInLegend+="\n" + cl.cropSymbol + ": " + cl.cropName;
            }
        }

        legend=cropsInLegend+intercropInLegend;

        TextView l = (TextView)findViewById(R.id.fieldLegend);
        l.setVisibility(View.VISIBLE);
        l.setText(legend);

        TextView dt = (TextView)findViewById(R.id.treatment1Legend);
        dt.setText("");
        dt = (TextView)findViewById(R.id.treatment2Legend);
        dt.setText("");
        dt = (TextView)findViewById(R.id.treatment3Legend);
        dt.setText("");
        dt = (TextView)findViewById(R.id.treatment4Legend);
        dt.setText("");

        int i=0;
        Iterator<String> iterator = treatmentLegends.iterator();
        while (iterator.hasNext()) {
            TextView tl= new TextView(this);
            String record = iterator.next();
            switch(i){
                case 0:
                    tl = (TextView)findViewById(R.id.treatment1Legend);
                    break;
                case 1:
                    tl = (TextView)findViewById(R.id.treatment2Legend);
                    break;
                case 2:
                    tl = (TextView)findViewById(R.id.treatment3Legend);
                    break;
                case 3:
                    tl = (TextView)findViewById(R.id.treatment4Legend);
                    break;
            }
            tl.setVisibility(View.VISIBLE);
            if(record.equals(treatmentNames[0])) {
                tl.setTextColor(agroHelper.getTreatmentColor(1));
            } else if(record.equals(treatmentNames[1])) {
                tl.setTextColor(agroHelper.getTreatmentColor(2));
            } else if(record.equals(treatmentNames[2])) {
                tl.setTextColor(agroHelper.getTreatmentColor(3));
            } else if(record.equals(treatmentNames[3])) {
                tl.setTextColor(agroHelper.getTreatmentColor(4));
            }
            tl.setText(record);
            i++;
        }

        if(preChosenPlots==0){
            String msg=this.getResources().getString(R.string.chosenXNotApplicable);
            msg=msg.replaceAll("x",task);
            Toast.makeText(this, msg, Toast.LENGTH_SHORT).show();
            Button enterData = (Button) findViewById(R.id.enterDataButton);
            enterData.setVisibility(View.GONE);
        }
    }

    boolean getPlotState(int n){
        boolean ret=false;
        Iterator<oPlotHelper> iterator = plotsInGrid.iterator();
        while (iterator.hasNext()) {
            oPlotHelper ph = iterator.next();
            if (ph.plotNumber == n) {
                //if choosable, toggle state. else, return same state
                ret = !ph.state;
                ph.state = ret;
                break;
            }
        }
        return ret;
    }

    void choosePlot(int n, View v){
        Button b = (Button)v;
        GradientDrawable d = (GradientDrawable) b.getBackground();

        if(getPlotState(n)) {
            d.setStroke(5, Color.RED);
            if(task.equals("measurement")){
                Button pb = (Button)previousPlot;
                GradientDrawable pd = (GradientDrawable) pb.getBackground();
                pd.setStroke(5, Color.WHITE);
                pb.setBackground(pd);
                getPlotState(previousPlotN);
                previousPlot=b;
                previousPlotN=n;
            }
        } else {
            if(!task.equals("measurement")) {
                d.setStroke(5, Color.WHITE);
            } else {
                getPlotState(n);
            }
        }

        b.setBackground(d);

    }

    public void enterData(View v){
        final Context context = this;
        Intent i;

        String plots="";
        // gather chosen plots
        int n=0;
        int nPlots=0;
        Iterator<oPlotHelper> iterator = plotsInGrid.iterator();
        while (iterator.hasNext()) {
            oPlotHelper ph = iterator.next();
            if(ph.state){
                if(plots.isEmpty()){
                    plots=Integer.toString(n);
                } else {
                    plots=plots+","+Integer.toString(n);
                }
                nPlots++;
            }
            n++;
        }
        if(plots.isEmpty()){
            Toast.makeText(this, R.string.noPlotsChosen, Toast.LENGTH_SHORT).show();
        } else {
            if(task.equals("input")){
                if(subTask.equals("crop")){
                    i = new Intent(context, enterCropInput.class);
                } else {
                    i = new Intent(context, enterTreatmentInput.class);
                }

                String longTitle = itemTitle+"\n"+agroHelper.getFieldNameFromId(field.fieldId)+"\nPlots ("+Integer.toString(nPlots)+"): "+agroHelper.getPlotNames(field,plots);

                i.putExtra("userId", userId);
                i.putExtra("userRole", userRole);
                i.putExtra("task", task);
                i.putExtra("subTask", subTask);
                i.putExtra("title", longTitle);
                i.putExtra("shortTitle",itemTitle);
                i.putExtra("taskId", taskId);
                i.putExtra("field", field.fieldId);
                i.putExtra("plots", plots);
                i.putExtra("update", "");

                startActivity(i);
                finish();

            } else if(task.equals("activity")){

                String longTitle = getString(R.string.titleEnterActivity)+"\n"+agroHelper.getFieldNameFromId(field.fieldId)+"\nPlots ("+Integer.toString(nPlots)+"): "+agroHelper.getPlotNames(field,plots);
                i = new Intent(context, enterActivity.class);

                i.putExtra("userId", userId);
                i.putExtra("userRole", userRole);
                i.putExtra("task", task);
                i.putExtra("title", longTitle);
                i.putExtra("shortTitle",getString(R.string.titleEnterActivity));
                i.putExtra("activity", taskId);
                i.putExtra("units","");
                i.putExtra("field", field.fieldId);
                i.putExtra("plots", plots);
                i.putExtra("update", "");

                startActivity(i);
                finish();

            } else if(task.equals("measurement")){

                String longTitle = itemTitle+"\n"+agroHelper.getFieldNameFromId(field.fieldId)+"\nPlot: "+agroHelper.getPlotNames(field,plots);
                i = new Intent(context, enterMeasurement.class);

                oMeasurement m = agroHelper.getMeasurementFromId(taskId);

                i.putExtra("userId", userId);
                i.putExtra("userRole", userRole);
                i.putExtra("task", task);
                i.putExtra("title", longTitle);
                i.putExtra("shortTitle",itemTitle);
                i.putExtra("measurementChosenCategory",measurementCategory);
                i.putExtra("field", field.fieldId);
                i.putExtra("plots", plots);
                i.putExtra("update", "");
                i.putExtra("measurement", taskId);
                i.putExtra("hasSamples",m.measurementHasSampleNumber);
                i.putExtra("units",m.measurementUnits);
                i.putExtra("type",m.measurementType);
                i.putExtra("min",m.measurementMin);
                i.putExtra("max",m.measurementMax);
                i.putExtra("categories",m.measurementCategories);

                startActivity(i);
                finish();

            }
        }
    }
}
