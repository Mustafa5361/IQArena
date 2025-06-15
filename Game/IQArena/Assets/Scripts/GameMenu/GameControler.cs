public abstract class GameControler
{

    public bool isSinglePlayer;

    public bool IsSinglePlayer
    {
        get
        {
            return isSinglePlayer;
        }
    }

    public GameControler(bool isSinglePlayer)
    {
        this.isSinglePlayer = isSinglePlayer;
    }

    public abstract Question GetQuestion();

    public abstract SetGameData Answer(GameMenu.Answer answer);



}