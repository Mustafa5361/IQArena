using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.SceneManagement;
using UnityEngine.UI;

public class GameMenu : MonoBehaviour
{
    
    [SerializeField] private GameObject Menu;
    [SerializeField] private GameObject QuitMenu;
    [SerializeField] private GameObject StartPanel;

    [SerializeField] private Text questionTxt;
    [SerializeField] private Text aBtnTxt;
    [SerializeField] private Text bBtnTxt;
    [SerializeField] private Text cBtnTxt;
    [SerializeField] private Text dBtnTxt;

    private static int roomID;

    Question question = new Question(-1);

    private void Start()
    {
        ApiGetQuestion(Answer.None);
    }

    public static void SetRoomID(int roomID)
    {
        GameMenu.roomID = roomID;
    }

    public void QuitPanel()
    {

        QuitMenu.SetActive(true);
    }

    public void QuitPanelClose()
    {
        StartPanel.SetActive(true);
        QuitMenu.SetActive(false);
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

        var writeQuestion = new WriteQuestion(GameManager.Token, question.questionID, AnswerToString(answer), roomID);

        Debug.Log(writeQuestion.ToString());

        ApiConnection.Connection<WriteQuestion, Question>("roomControler.php", writeQuestion, QuestionUpdate);

    }

    public void QuestionUpdate(Question question)
    {

        this.question = question;

        MenuUpdate(question);

    }

    void MenuUpdate(Question question)
    {

        Debug.Log(question.ToString());

        questionTxt.text = question.question;
        aBtnTxt.text = question.answerA;
        bBtnTxt.text = question.answerB;
        cBtnTxt.text = question.answerC;
        dBtnTxt.text = question.answerD;

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

    private string AnswerToString(Answer answer)
    {

        switch (answer)
        {
            case Answer.A:
                return "A";
            case Answer.B:
                return "B";
            case Answer.C:
                return "C";
            case Answer.D:
                return "D";
            default:
                return "None";
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
