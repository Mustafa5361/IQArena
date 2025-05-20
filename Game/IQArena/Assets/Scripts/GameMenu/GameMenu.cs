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

    Question question;

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

        ApiConnection.Connection<WriteQuestion, Question>("roomControler.php", new WriteQuestion(GameManager.token, question.questionID, answer, roomID), QuestionUpdate);

    }

    public void QuestionUpdate(Question question)
    {

        this.question = question;

        MenuUpdate(question);

    }

    void MenuUpdate(Question question)
    {

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

    public enum Answer
    {
        None,
        A,
        B,
        C,
        D,
    }

}
