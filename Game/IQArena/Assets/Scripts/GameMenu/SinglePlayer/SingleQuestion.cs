[System.Serializable]
public class SingleQuesiton : Question
{

    public string correctAnswer;

}

[System.Serializable]
public class GetSingleQuestion
{

    public SingleQuesiton[] questions = new SingleQuesiton[10];

}