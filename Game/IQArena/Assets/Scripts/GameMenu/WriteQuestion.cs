public class WriteQuestion
{
    public string token;
    public int questionID;
    public GameMenu.Answer answer;
    public int roomID;

    public WriteQuestion(string token, int questionID, GameMenu.Answer answer, int roomID)
    {

        this.token = token;
        this.questionID = questionID;
        this.answer = answer;
        this.roomID = roomID;

    }

}