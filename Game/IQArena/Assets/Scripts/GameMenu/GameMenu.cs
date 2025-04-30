using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.SceneManagement;

public class GameMenu : MonoBehaviour
{
    
    [SerializeField] private GameObject Menu;

    private static int roomID;

    Question question;

    public static void SetRoomID(int roomID)
    {
        GameMenu.roomID = roomID;
    }

    public void MainMenuOpen()
    {
        roomID = 0;
        SceneManager.LoadScene("MainMenu");
    }
    
    public void answerButton(GameObject btn)
    {

        ApiGetQuestion(StringToAnswer(btn.name));

    }

    public void ApiGetQuestion(Answer answer)
    {

        ApiConnection.Connection<WriteQuestion, Question>("roomControler.php", new WriteQuestion(GameManager.token, question.questionID, answer, roomID), QuestionUpdate);

    }

    public void QuestionUpdate(Question question)
    {

        this.question = question;

        //ekrana veriler yazdýrýlacak

    }

    private Answer StringToAnswer(string str)
    {

        switch (str)
        {
            case "A":
                return Answer.A;
            case "B":
                return Answer.B;
            case "C":
                return Answer.C;
            case "D":
                return Answer.D;
            default:
                return Answer.None;

        }

    }

    public enum Answer
    {
        None,
        A,
        B,
        C,
        D,
    }

}
