using System.Collections.Generic;

public class PlayerRank
{

    public List<GetRank> ranks { get; set; }

    public class GetRank
    {

        public string username { get; set; }
        public int cup { get; set; }

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