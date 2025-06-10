[System.Serializable]
public class GameFinishData
{

    public bool finished;
    public string thisUsername;
    public string enemyUsername;
    public string thisPoint;
    public string enemyPoint;
    public string thisCupChange;
    public string thisStatus;

    public override string ToString()
    {
        return finished + " / " + thisCupChange + " / " + thisUsername + " / " + thisPoint;
    }

}