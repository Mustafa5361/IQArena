public class SinglePlayerControler : GameControler
{

    SingleQuesiton[] questions = new SingleQuesiton[10];

    private int index = 0;



    public SinglePlayerControler(bool isSinglePlayer) : base(isSinglePlayer) { }

    public override Question GetQuestion()
    {
        
        if (questions[index] != null)
            return questions[index];

        throw new System.Exception("sorular bitti.");

    }

    public override SetGameData Answer(GameMenu.Answer answer)
    {
        throw new System.NotImplementedException();
    }
}