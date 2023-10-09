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
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;
import java.util.TimeZone;

/**
 * Created by Eugenio on 25/04/2017.
 */
public class enterCropInput extends AppCompatActivity {

    public int userId;
    public int userRole;
    public String task;
    public String subTask;
    public int inputLogId;
    public int fieldId;
    public String plots;
    public int cropId;
    public String inputTitle;
    public String shortTitle;
    public String update;

    public Date cropInputDate;

    public boolean changes=false;
    public int exitAction;

    String ageNumber;
    String originText;
    float quantityNumber;
    String costNumber;
    String commentsText;
    String unitsText;
    String varietyText;

    @Override
    public void onCreate(Bundle savedInstanceState) {

        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_enter_crop_input);

        userId = getIntent().getExtras().getInt("userId");
        userRole = getIntent().getExtras().getInt("userRole");
        task = getIntent().getExtras().getString("task");
        subTask = getIntent().getExtras().getString("subTask");
        fieldId = getIntent().getExtras().getInt("field");
        plots = getIntent().getExtras().getString("plots");
        cropId = getIntent().getExtras().getInt("taskId");
        inputTitle = getIntent().getExtras().getString("title");
        shortTitle = getIntent().getExtras().getString("shortTitle");
        update = getIntent().getExtras().getString("update");

        TextView tt = (TextView)findViewById(R.id.cropInputTitle);
        tt.setText(inputTitle);

        EditText age = (EditText) findViewById(R.id.cropAge);
        EditText quantity = (EditText) findViewById(R.id.cropQuantity);
        EditText cost = (EditText) findViewById(R.id.cropCost);
        EditText origin = (EditText) findViewById(R.id.cropOrigin);
        EditText comments = (EditText) findViewById(R.id.inputComments);
        EditText variety =(EditText) findViewById(R.id.cropVariety);
        EditText units = (EditText) findViewById(R.id.cropUnits);

        if(update.equals("crop")){
            inputLogId = getIntent().getExtras().getInt("inputLogId");

            Button ob = (Button)findViewById(R.id.okButton);
            ob.setText(R.string.editButtonText);

            Button db = (Button)findViewById(R.id.dateButton);
            db.setText(getIntent().getExtras().getString("cropInputDate"));
            cropInputDate = stringToDate(getIntent().getExtras().getString("cropInputDate"));

            age.setText(getIntent().getExtras().getString("cropInputAge"));
            quantity.setText(Float.toString(getIntent().getExtras().getFloat("cropInputQuantity")));
            cost.setText(getIntent().getExtras().getString("cropInputCost"));
            origin.setText(getIntent().getExtras().getString("cropInputOrigin"));
            comments.setText(getIntent().getExtras().getString("cropInputComments"));
            variety.setText(getIntent().getExtras().getString("cropInputVariety"));
            units.setText(getIntent().getExtras().getString("cropInputUnits"));
        } else {
            cropInputDate = new Date();
        }

        age.addTextChangedListener(new TextWatcher() {
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

        quantity.addTextChangedListener(new TextWatcher() {
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

        origin.addTextChangedListener(new TextWatcher() {
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

        variety.addTextChangedListener(new TextWatcher() {
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

        units.addTextChangedListener(new TextWatcher() {
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
        cb.setText(dateToString(cropInputDate));
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
            i.putExtra("cropId", cropId);
            i.putExtra("treatmentId", -1);
            i.putExtra("field", fieldId);
            i.putExtra("plots", plots);
            i.putExtra("newCropInput", false);
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
        calActivity.setTime(cropInputDate);
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

                cropInputDate = calendar.getTime();

                Button cb = (Button)findViewById(R.id.dateButton);
                cb.setText(dateToString(cropInputDate));
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

    public void registerCrop(View v) {
        EditText age = (EditText) findViewById(R.id.cropAge);
        String ageValue = String.valueOf(age.getText());
        if (isNumeric(ageValue) || ageValue.isEmpty()) {
            ageNumber = ageValue;

            EditText quantity = (EditText) findViewById(R.id.cropQuantity);
            String quantityValue = String.valueOf(quantity.getText());
            if (isNumeric(quantityValue)) {

                quantityNumber = Float.parseFloat(quantityValue);

                EditText units = (EditText) findViewById(R.id.cropUnits);
                unitsText = String.valueOf(units.getText());
                if(!unitsText.isEmpty()) {

                    EditText cost = (EditText) findViewById(R.id.cropCost);
                    String costValue = String.valueOf(cost.getText());
                    if (isNumeric(costValue) || costValue.isEmpty()) {

                        costNumber = costValue;

                        EditText origin = (EditText) findViewById(R.id.cropOrigin);
                        originText = String.valueOf(origin.getText());

                        if (!originText.isEmpty()) {
                            originText = originText.replaceAll(";", " ");
                            originText = originText.replaceAll("\\|", " ");
                            originText = originText.replaceAll("\\*", " ");
                        }

                        EditText variety = (EditText) findViewById(R.id.cropVariety);
                        varietyText = String.valueOf(variety.getText());

                        if (!varietyText.isEmpty()) {
                            varietyText = varietyText.replaceAll(";", " ");
                            varietyText = varietyText.replaceAll("\\|", " ");
                            varietyText = varietyText.replaceAll("\\*", " ");
                        }

                        EditText comments = (EditText) findViewById(R.id.inputComments);
                        commentsText = String.valueOf(comments.getText());

                        if (!commentsText.isEmpty()) {
                            commentsText = commentsText.replaceAll(";", " ");
                            commentsText = commentsText.replaceAll("\\|", " ");
                            commentsText = commentsText.replaceAll("\\*", " ");
                        }

                        if (update.equals("")) {
                            requestCopyToReplications();
                        } else {
                            Toast.makeText(this, "Input edited successfully", Toast.LENGTH_SHORT).show();
                            Intent i = new Intent(this, manageData.class);
                            i.putExtra("userId", userId);
                            i.putExtra("userRole", userRole);
                            i.putExtra("task", task);
                            i.putExtra("inputLogId", inputLogId);
                            i.putExtra("update", "cropInput");
                            i.putExtra("crop", cropId);
                            i.putExtra("cropInputDate", dateToString(cropInputDate));
                            i.putExtra("cropInputAge", ageNumber);
                            i.putExtra("cropInputOrigin", originText);
                            i.putExtra("cropInputVariety", varietyText);
                            i.putExtra("cropInputQuantity", quantityNumber);
                            i.putExtra("cropInputUnits", unitsText);
                            i.putExtra("cropInputCost", costNumber);
                            i.putExtra("cropInputComments", commentsText);
                            startActivity(i);
                            finish();
                        }
                    } else {
                        Toast.makeText(this, R.string.enterValidNumberText, Toast.LENGTH_SHORT).show();
                        cost.requestFocus();
                    }
                } else {
                    Toast.makeText(this, R.string.enterValidTextText, Toast.LENGTH_SHORT).show();
                    units.requestFocus();
                }
            } else {
                Toast.makeText(this, R.string.enterValidNumberText, Toast.LENGTH_SHORT).show();
                quantity.requestFocus();
            }
        } else {
            Toast.makeText(this, R.string.enterValidNumberText, Toast.LENGTH_SHORT).show();
            age.requestFocus();
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

    public void doSave(boolean copy){
        Toast.makeText(this, "Input saved successfully", Toast.LENGTH_SHORT).show();
        Intent i = new Intent(this, inputChooser.class);
        i.putExtra("userId", userId);
        i.putExtra("userRole", userRole);
        i.putExtra("task", task);
        i.putExtra("title", shortTitle);
        i.putExtra("field", fieldId);
        i.putExtra("plots", plots);
        i.putExtra("newCropInput", true);
        i.putExtra("cropId", cropId);
        i.putExtra("treatmentId", -1);
        i.putExtra("cropInputDate", dateToString(cropInputDate));
        i.putExtra("cropInputAge", ageNumber);
        i.putExtra("cropInputOrigin", originText);
        i.putExtra("cropInputVariety", varietyText);
        i.putExtra("cropInputQuantity", quantityNumber);
        i.putExtra("cropInputUnits", unitsText);
        i.putExtra("cropInputCost", costNumber);
        i.putExtra("cropInputComments", commentsText);
        i.putExtra("copy",copy);
        startActivity(i);
        finish();
    }
}
