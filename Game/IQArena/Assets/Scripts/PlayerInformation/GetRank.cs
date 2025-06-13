[System.Serializable]
public class PlayerRank
{

    public GetRank[] ranks = new GetRank[100];

    [System.Serializable]
    public class GetRank
    {

        public string username;
        public int cup;
        public int point;

        public override string ToString()
        {
            return username;
        }

    }

    public override string ToString()
    {
        return ranks[0].ToString();
    }

}

public class SetRank
{

    public string rank;

    public SetRank(string rank)
    {
        this.rank = rank;
    }

}

public class PlayerProfil
{


    public class GetProfil
    {

        

    }

}