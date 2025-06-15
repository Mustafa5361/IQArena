[System.Serializable]
public class SingleQuesiton : Question
{

    public string currentAnswer;

}

[System.Serializable]
public class GetSingleQuestion
{

    public SingleQuesiton[] questions = new SingleQuesiton[10];

}