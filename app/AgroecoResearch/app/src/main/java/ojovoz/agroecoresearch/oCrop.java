package ojovoz.agroecoresearch;

/**
 * Created by Eugenio on 31/03/2017.
 */
public class oCrop {
    public int cropId;
    public String cropName;
    public String cropVariety;
    public String cropSymbol;

    oCrop(int id, String name, String variety, String symbol){
        cropId=id;
        cropName=name;
        cropVariety=variety;
        cropSymbol=symbol;
    }

    oCrop(){

    }
}
