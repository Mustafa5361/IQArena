using System;
using System.Collections;
using System.Collections.Generic;
using TMPro;
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

    [SerializeField] private GameObject finishPanel;

    [SerializeField] private GameObject finishPanelPlayer;
    [SerializeField] private GameObject finishPanelEnemy;

    [SerializeField] private TextMeshProUGUI WinOrLose;
    [SerializeField] private TextMeshProUGUI CupQuentity;

    Coroutine finish;

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

        ApiConnection.Connection<WriteQuestion, SetGameData>("roomControler.php", writeQuestion, SetDataApi);

    }

    public void SetDataApi(SetGameData gameData)
    {

        if (gameData.Question != null && gameData.Question.questionID > 0)
            QuestionUpdate(gameData.Question);
        else
            GameOverControl(gameData.Finish);

    }

    public void GameOverControl(GameFinishData finishData)
    {

        Debug.Log("Finished = " + finishData.ToString());
        if (finishData.finished)
        {

            StopCoroutine(finish);
            GameOverPanelSetData(finishData);

        }
        else
            if (finish == null)
                finish = StartCoroutine(FinishDataGetApi());


    }

    IEnumerator FinishDataGetApi()
    {

        while (true)
        {

            ApiConnection.Connection<SetData, SetGameData>("matchFinished.php", new SetData(roomID, GameManager.Token), SetDataApi);

            yield return new WaitForSeconds(3f);

        }

    }

    private void GameOverPanelSetData(GameFinishData finish)
    {
         
        finishPanel.SetActive(true);

        if (finish.thisStatus.ToLower() == "win")
        {

            WinOrLose.text = "WINNER";
            WinOrLose.color = Color.green;

        }
        else
        {

            WinOrLose.text = "LOSE";
            WinOrLose.color = Color.red;

        }

        CupQuentity.text = finish.thisCupChange;


        finishPanelPlayer.GetComponent<FinishDataWrite>().CreateDates(
            finish.thisUsername,
            new FinishDataWrite.Data("Point", finish.thisPoint)
            );

        finishPanelEnemy.GetComponent<FinishDataWrite>().CreateDates(
            "Point",
            new FinishDataWrite.Data("Point", finish.enemyPoint)
            );

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

    //oyun onu verilerini istemek için.
    class SetData
    {

        public string token;
        public int roomID;

        public SetData(int roomID, string token)
        {
            this.roomID = roomID;
            this.token = token;
        }

    }

}
