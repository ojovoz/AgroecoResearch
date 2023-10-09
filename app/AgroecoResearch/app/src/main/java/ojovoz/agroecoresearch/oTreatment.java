package ojovoz.agroecoresearch;

/**
 * Created by Eugenio on 31/03/2017.
 */
public class oTreatment {
    public int treatmentId;
    public String treatmentName;
    public String treatmentCategory;
    public oCrop primaryCrop;
    public oCrop intercroppingCrop;

    oTreatment(int id, String name, String category, oCrop c, oCrop l){
        treatmentId=id;
        treatmentName=name;
        treatmentCategory=category;
        primaryCrop=c;
        intercroppingCrop=l;
    }

    oTreatment(){

    }
}
