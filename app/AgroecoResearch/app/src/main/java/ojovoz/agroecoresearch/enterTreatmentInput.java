package ojovoz.agroecoresearch;

import android.app.AlertDialog;
import android.app.Dialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
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
import android.widget.AutoCompleteTextView;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.ScrollView;
import android.widget.TableLayout;
import android.widget.TableRow;
import android.widget.TextView;
import android.widget.Toast;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.Iterator;
import java.util.TimeZone;

/**
 * Created by Eugenio on 25/04/2017.
 */
public class enterTreatmentInput extends AppCompatActivity {

    public int userId;
    public int userRole;
    public String task;
    public String subTask;
    public int inputLogId;
    public int fieldId;
    public String plots;
    public int treatmentId;
    public String inputTitle;
    public String shortTitle;
    public String update;

    public Date treatmentInputDate;

    public boolean changes=false;
    public int exitAction;

    String materialText;
    String methodText;
    String costNumber;
    String commentsText;
    String unitsText;

    public preferenceManager prefs;
    public ArrayList<String> previousMethods;
    public ArrayList<String> previousMaterials;

    public ArrayList<oIngredientHelper> ingredients;
    boolean bDeleting = false;
    String ingredientsString="";

    @Override
    public void onCreate(Bundle savedInstanceState) {

        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_enter_treatment_input);

        userId = getIntent().getExtras().getInt("userId");
        userRole = getIntent().getExtras().getInt("userRole");
        task = getIntent().getExtras().getString("task");
        subTask = getIntent().getExtras().getString("subTask");
        fieldId = getIntent().getExtras().getInt("field");
        plots = getIntent().getExtras().getString("plots");
        treatmentId = getIntent().getExtras().getInt("taskId");
        inputTitle = getIntent().getExtras().getString("title");
        shortTitle = getIntent().getExtras().getString("shortTitle");
        update = getIntent().getExtras().getString("update");

        TextView tt = (TextView)findViewById(R.id.treatmentInputTitle);
        tt.setText(inputTitle);

        prefs = new preferenceManager(this);
        previousMethods = prefs.getArrayListPreference("previousMethods");
        previousMaterials = prefs.getArrayListPreference("previousMaterials");

        EditText cost = (EditText) findViewById(R.id.treatmentCost);
        EditText method = (EditText) findViewById(R.id.treatmentPreparationMethod);
        EditText comments = (EditText) findViewById(R.id.inputComments);

        if(update.equals("treatment")){
            inputLogId = getIntent().getExtras().getInt("inputLogId");

            Button ob = (Button)findViewById(R.id.okButton);
            ob.setText(R.string.editButtonText);

            Button db = (Button)findViewById(R.id.dateButton);
            db.setText(getIntent().getExtras().getString("treatmentInputDate"));
            treatmentInputDate = stringToDate(getIntent().getExtras().getString("treatmentInputDate"));

            cost.setText(getIntent().getExtras().getString("treatmentInputCost"));
            String materials = getIntent().getExtras().getString("treatmentInputMaterial");
            method.setText(getIntent().getExtras().getString("treatmentInputMethod"));
            comments.setText(getIntent().getExtras().getString("treatmentInputComments"));
            initializeIngredientsTable(materials);
        } else {
            treatmentInputDate = new Date();
            initializeIngredientsTable("");
        }

        comments.addTextChangedListener(new TextWatcher() {
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

        cost.addTextChangedListener(new TextWatcher() {
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

        method.addTextChangedListener(new TextWatcher() {
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
        cb.setText(dateToString(treatmentInputDate));
        cb.setOnClickListener(new View.OnClickListener(){
            @Override
            public void onClick(View v){
                displayDatePicker();
            }
        });

        if(previousMethods.size()>0) {
            AutoCompleteAdapter adapter = new AutoCompleteAdapter(this, android.R.layout.simple_dropdown_item_1line, android.R.id.text1, previousMethods);
            AutoCompleteTextView a = (AutoCompleteTextView) findViewById(R.id.treatmentPreparationMethod);
            a.setAdapter(adapter);
            a.setImeOptions(EditorInfo.IME_ACTION_NEXT);
        } else {
            AutoCompleteTextView a = (AutoCompleteTextView) findViewById(R.id.treatmentPreparationMethod);
            a.setImeOptions(EditorInfo.IME_ACTION_NEXT);
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

    public void goBack(){
        final Context context = this;
        if(update.equals("")) {
            Intent i = new Intent(context, chooseFieldPlot.class);
            i.putExtra("userId", userId);
            i.putExtra("userRole", userRole);
            i.putExtra("task", task);
            i.putExtra("treatmentId", treatmentId);
            i.putExtra("cropId", -1);
            i.putExtra("field", fieldId);
            i.putExtra("plots", plots);
            i.putExtra("newTreatmentInput", false);
            i.putExtra("title", shortTitle);
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

    public void initializeIngredientsTable(String ini) {
        oIngredientHelper ih;
        ingredients = new ArrayList<>();
        if(ini.isEmpty()) {
            ih = new oIngredientHelper();
            ih.ingredient="";
            ih.quantity=0.0f;
            ih.units="kg";
            ingredients.add(ih);
        } else {
            String[] sampleItems = ini.split("\\*");
            for(int i=0;i<sampleItems.length;i+=3){
                ih = new oIngredientHelper();
                ih.ingredient = sampleItems[i];
                ih.quantity = Float.valueOf(sampleItems[i+1]);
                ih.units = sampleItems[i+2];
                ingredients.add(ih);
            }
        }

        fillIngredientsTable();

    }

    public void fillIngredientsTable(){
        int n = 0;
        TableLayout ingredientsTable = (TableLayout) findViewById(R.id.ingredientsTable);
        ingredientsTable.removeAllViews();
        Iterator<oIngredientHelper> iterator = ingredients.iterator();
        while (iterator.hasNext()) {
            oIngredientHelper ih = iterator.next();

            final TableRow trow = new TableRow(enterTreatmentInput.this);
            TableRow.LayoutParams lp = new TableRow.LayoutParams(TableRow.LayoutParams.MATCH_PARENT, TableRow.LayoutParams.MATCH_PARENT, 1.0f);
            lp.setMargins(10, 10, 0, 10);
            if (n % 2 == 0) {
                trow.setBackgroundColor(ContextCompat.getColor(this, R.color.lightGray));
            } else {
                trow.setBackgroundColor(ContextCompat.getColor(this, R.color.colorWhite));
            }

            CheckBox cb = new CheckBox(enterTreatmentInput.this);
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
                            deleteIngredient(view);
                        }
                    },200);
                }
            });
            trow.addView(cb, lp);

            AutoCompleteTextView in = new AutoCompleteTextView(enterTreatmentInput.this);
            if(previousMaterials.size()>0) {
                AutoCompleteAdapter adapter = new AutoCompleteAdapter(this, android.R.layout.simple_dropdown_item_1line, android.R.id.text1, previousMaterials);
                in.setAdapter(adapter);
            }
            in.setTextSize(TypedValue.COMPLEX_UNIT_DIP, 16f);
            in.setText(ih.ingredient);
            in.setTextAlignment(View.TEXT_ALIGNMENT_CENTER);
            in.setImeOptions(EditorInfo.IME_ACTION_NEXT);
            in.setRawInputType(InputType.TYPE_CLASS_TEXT);
            in.setMaxLines(1);
            in.setPadding(0,10,0,10);
            in.setId(n);
            in.setOnFocusChangeListener(new View.OnFocusChangeListener() {
                @Override
                public void onFocusChange(View view, boolean b) {
                    if (!b) {
                        updateIngredientName(view);
                    }
                }
            });
            in.setOnTouchListener(new View.OnTouchListener() {
                @Override
                public boolean onTouch(View view, MotionEvent motionEvent) {
                    findViewById(R.id.childScrollView).getParent().requestDisallowInterceptTouchEvent(true);
                    return false;
                }
            });
            in.addTextChangedListener(new TextWatcher() {
                @Override
                public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) {

                }

                @Override
                public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) {
                    changes = true;
                }

                @Override
                public void afterTextChanged(Editable editable) {
                    for(int i = editable.length(); i > 0; i--) {

                        if(editable.subSequence(i-1, i).toString().equals("\n"))
                            editable.replace(i-1, i, "");
                    }
                }
            });
            trow.addView(in, lp);

            EditText iq = new EditText(enterTreatmentInput.this);
            iq.setTextSize(TypedValue.COMPLEX_UNIT_DIP, 16f);
            iq.setText(String.valueOf(ih.quantity));
            iq.setTextAlignment(View.TEXT_ALIGNMENT_CENTER);
            iq.setRawInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
            iq.setImeOptions(EditorInfo.IME_ACTION_NEXT);
            iq.setPadding(0,10,0,10);
            iq.setId(n);
            iq.setOnFocusChangeListener(new View.OnFocusChangeListener() {
                @Override
                public void onFocusChange(View view, boolean b) {
                    if (!b) {
                        updateIngredientQuantity(view);
                    }
                }
            });
            iq.setOnTouchListener(new View.OnTouchListener() {
                @Override
                public boolean onTouch(View view, MotionEvent motionEvent) {
                    findViewById(R.id.childScrollView).getParent().requestDisallowInterceptTouchEvent(true);
                    return false;
                }
            });
            iq.addTextChangedListener(new TextWatcher() {
                @Override
                public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) {

                }

                @Override
                public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) {
                    changes = true;
                }

                @Override
                public void afterTextChanged(Editable editable) {

                }
            });
            trow.addView(iq, lp);

            EditText iu = new EditText(enterTreatmentInput.this);
            iu.setTextSize(TypedValue.COMPLEX_UNIT_DIP, 16f);
            iu.setText(String.valueOf(ih.units));
            iu.setTextAlignment(View.TEXT_ALIGNMENT_CENTER);
            iu.setImeOptions(EditorInfo.IME_ACTION_DONE);
            iu.setRawInputType(InputType.TYPE_CLASS_TEXT);
            iu.setMaxLines(1);
            iu.setPadding(0,10,0,10);
            iu.setId(n);
            iu.setOnFocusChangeListener(new View.OnFocusChangeListener() {
                @Override
                public void onFocusChange(View view, boolean b) {
                    if (!b) {
                        updateIngredientUnits(view);
                    }
                }
            });
            iu.setOnTouchListener(new View.OnTouchListener() {
                @Override
                public boolean onTouch(View view, MotionEvent motionEvent) {
                    findViewById(R.id.childScrollView).getParent().requestDisallowInterceptTouchEvent(true);
                    return false;
                }
            });
            iu.addTextChangedListener(new TextWatcher() {
                @Override
                public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) {

                }

                @Override
                public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) {
                    changes = true;
                }

                @Override
                public void afterTextChanged(Editable editable) {
                    for(int i = editable.length(); i > 0; i--) {

                        if(editable.subSequence(i-1, i).toString().equals("\n"))
                            editable.replace(i-1, i, "");
                    }

                }
            });
            trow.addView(iu, lp);

            trow.setGravity(Gravity.CENTER_VERTICAL);
            ingredientsTable.addView(trow, lp);
            n++;
        }
    }

    public void updateIngredientName(View view) {
        if(!bDeleting) {
            EditText e = (EditText) view;
            int id = e.getId();
            String value = String.valueOf(e.getText());

            if (!value.isEmpty()) {
                oIngredientHelper ih = ingredients.get(id);
                ih.ingredient = value;
            }
        } else {
            bDeleting=false;
        }
    }

    public void updateIngredientQuantity(View view) {
        if(!bDeleting) {
            EditText e = (EditText) view;
            int id = e.getId();
            String value = String.valueOf(e.getText());

            if (!value.isEmpty()) {
                oIngredientHelper ih = ingredients.get(id);
                if (!isNumeric(value) || Float.parseFloat(value) < 0.0f) {
                    String msg = this.getResources().getString(R.string.ingredientQuantityOutOfRange);
                    msg = msg.replaceAll("x", String.valueOf(ih.ingredient));
                    Toast.makeText(this, msg, Toast.LENGTH_SHORT).show();
                } else {
                    ih.quantity = Float.valueOf(value);
                }
            }
        } else {
            bDeleting=false;
        }
    }

    public void updateIngredientUnits(View view) {
        if(!bDeleting) {
            EditText e = (EditText) view;
            int id = e.getId();
            String value = String.valueOf(e.getText());

            if (!value.isEmpty()) {
                oIngredientHelper ih = ingredients.get(id);
                ih.units = value;
            }
        } else {
            bDeleting=false;
        }
    }

    public void deleteIngredient(View v){

        InputMethodManager imm = (InputMethodManager)getSystemService(Context.INPUT_METHOD_SERVICE);
        imm.hideSoftInputFromWindow(v.getWindowToken(), 0);

        final CheckBox c = (CheckBox)v;
        c.setChecked(true);
        final int deleteId = c.getId();
        oIngredientHelper ih = ingredients.get(deleteId);

        String msg = this.getResources().getString(R.string.deleteIngredientString);
        msg = msg.replaceAll("x", String.valueOf(ih.ingredient));

        AlertDialog.Builder logoutDialog = new AlertDialog.Builder(this);
        logoutDialog.setTitle(R.string.deleteIngredientTitle);
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
        ingredients.remove(id);
        fillIngredientsTable();
    }

    public void addIngredient( View v) {
        oIngredientHelper ih = new oIngredientHelper();
        ih.ingredient="";
        ih.quantity=0.0f;
        ih.units="kg";
        ingredients.add(ih);

        changes=true;

        fillIngredientsTable();

        final ScrollView sv = (ScrollView)findViewById(R.id.childScrollView);
        sv.postDelayed(new Runnable() { @Override public void run() { sv.fullScroll(View.FOCUS_DOWN); } }, 500);
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
        calActivity.setTime(treatmentInputDate);
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

                treatmentInputDate = calendar.getTime();

                Button cb = (Button)findViewById(R.id.dateButton);
                cb.setText(dateToString(treatmentInputDate));
                dialog.dismiss();
                changes=true;
            }
        });
        dialog.show();
    }

    public String dateToString(Date d){
        SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd");
        sdf.setTimeZone(TimeZone.getDefault());
        return sdf.format(d);
    }

    public boolean isNumeric(String str) {
        return str.matches("-?\\d+(\\.\\d+)?");
    }

    public boolean VerifyIngredients(){
        boolean ret=true;
        ingredientsString="";
        int n=1;
        Iterator<oIngredientHelper> iterator = ingredients.iterator();
        while (iterator.hasNext()) {
            oIngredientHelper ih = iterator.next();
            if(ih.ingredient.isEmpty() || ih.units.isEmpty() || Float.valueOf(ih.quantity)<0){
                String msg = this.getResources().getString(R.string.emptyIngredientMessage);
                msg = msg.replaceAll("x", String.valueOf(n));
                Toast.makeText(this, msg, Toast.LENGTH_SHORT).show();
                ret=false;
                break;
            } else {
                String ingredient = ih.ingredient.replaceAll(";", " ");
                ingredient = ingredient.replaceAll("\\|", " ");
                ingredient = ingredient.replaceAll("\\*", " ");
                prefs.appendIfNotExists("previousMaterials", ingredient);
                String units = ih.units.replaceAll(";", " ");
                units = units.replaceAll("\\|", " ");
                units = units.replaceAll("\\*", " ");
                String quantity = String.valueOf(ih.quantity);
                if(ingredientsString.isEmpty()){
                    ingredientsString=ingredient+"*"+quantity+"*"+units;
                } else {
                    ingredientsString=ingredientsString+"*"+ingredient+"*"+quantity+"*"+units;
                }
            }
            n++;
        }
        return ret;
    }

    public void registerTreatment(View v) {

        EditText c = (EditText)findViewById(R.id.inputComments);
        c.requestFocus();

        if(VerifyIngredients()) {

            EditText cost = (EditText) findViewById(R.id.treatmentCost);
            String costValue = String.valueOf(cost.getText());
            if (isNumeric(costValue) || costValue.isEmpty()) {

                costNumber = costValue;

                EditText method = (EditText) findViewById(R.id.treatmentPreparationMethod);
                methodText = String.valueOf(method.getText());
                if (!methodText.isEmpty()) {
                    methodText = methodText.replaceAll(";", " ");
                    methodText = methodText.replaceAll("\\|", " ");
                    methodText = methodText.replaceAll("\\*", " ");
                }

                commentsText = String.valueOf(c.getText());
                if (!commentsText.isEmpty()) {
                    commentsText = commentsText.replaceAll(";", " ");
                    commentsText = commentsText.replaceAll("\\|", " ");
                    commentsText = commentsText.replaceAll("\\*", " ");
                }

                if (update.equals("")) {
                    requestCopyToReplications();
                } else {
                    prefs.appendIfNotExists("previousMethods", methodText);
                    Toast.makeText(this, "Input edited successfully", Toast.LENGTH_SHORT).show();
                    Intent i = new Intent(this, manageData.class);
                    i.putExtra("userId", userId);
                    i.putExtra("userRole", userRole);
                    i.putExtra("task", task);
                    i.putExtra("inputLogId", inputLogId);
                    i.putExtra("update", "treatmentInput");
                    i.putExtra("treatment", treatmentId);
                    i.putExtra("treatmentInputDate", dateToString(treatmentInputDate));
                    i.putExtra("treatmentInputMaterial", ingredientsString);
                    i.putExtra("treatmentInputQuantity", 0.0f);
                    i.putExtra("treatmentInputUnits", "");
                    i.putExtra("treatmentInputMethod", methodText);
                    i.putExtra("treatmentInputCost", costNumber);
                    i.putExtra("treatmentInputComments", commentsText);
                    startActivity(i);
                    finish();
                }

            } else {
                Toast.makeText(this, R.string.enterValidNumberText, Toast.LENGTH_SHORT).show();
                cost.requestFocus();
            }
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

    void doSave(boolean copy){
        prefs.appendIfNotExists("previousMethods",methodText);
        prefs.appendIfNotExists("previousMaterials",materialText);
        Toast.makeText(this, "Input saved successfully", Toast.LENGTH_SHORT).show();
        Intent i = new Intent(this, inputChooser.class);
        i.putExtra("userId", userId);
        i.putExtra("userRole", userRole);
        i.putExtra("task", task);
        i.putExtra("field", fieldId);
        i.putExtra("plots", plots);
        i.putExtra("title", shortTitle);
        i.putExtra("newTreatmentInput", true);
        i.putExtra("treatmentId", treatmentId);
        i.putExtra("cropId",-1);
        i.putExtra("treatmentInputDate", dateToString(treatmentInputDate));
        i.putExtra("treatmentInputMaterial", ingredientsString);
        i.putExtra("treatmentInputQuantity", 0.0f);
        i.putExtra("treatmentInputUnits", "");
        i.putExtra("treatmentInputMethod", methodText);
        i.putExtra("treatmentInputCost", costNumber);
        i.putExtra("treatmentInputComments", commentsText);
        i.putExtra("copy",copy);
        startActivity(i);
        finish();
    }
}
