public class Question
{
    public int questionID {  get; set; }
    public string question {  get; set; }
    public string answerA { get; set; }
    public string answerB { get; set; }
    public string answerC { get; set; }
    public string answerD { get; set; }

    public Question() { }

    public Question(int questionID)
    {
        this.questionID = questionID;
    }

}