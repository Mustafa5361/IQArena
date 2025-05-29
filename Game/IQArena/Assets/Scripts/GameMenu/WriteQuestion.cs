public class WriteQuestion
{
    public string token;
    public int questionID;
    public string answer;
    public int roomID;

    public WriteQuestion(string token, int questionID, string answer, int roomID)
    {

        this.token = token;
        this.questionID = questionID;
        this.answer = answer;
        this.roomID = roomID;

    }

    public override string ToString()
    {
        return token + " / " + questionID + " / " + answer + " / " + roomID; 
    }

}