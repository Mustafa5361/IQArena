[System.Serializable]
public class Question
{
    public int questionID;
    public string question;
    public string answerA;
    public string answerB;
    public string answerC;
    public string answerD;

    public Question() { }

    public Question(int questionID)
    {
        this.questionID = questionID;
    }

    public override string ToString()
    {
        return questionID + " / " + question;
    }

}