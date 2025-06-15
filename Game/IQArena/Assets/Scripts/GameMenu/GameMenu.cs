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

    #region multiPlayer

    [SerializeField] private GameObject finishPanel;

    [SerializeField] private GameObject finishPanelPlayer;
    [SerializeField] private GameObject finishPanelEnemy;

    [SerializeField] private TextMeshProUGUI WinOrLose;
    [SerializeField] private TextMeshProUGUI CupQuentity;

    Coroutine finish;

    #endregion

    [SerializeField] private GameObject singleFinishPanel;
    [SerializeField] private GameObject singleData;

    private int startTime;

    private static int roomID;
    public static List<SingleQuesiton> singleQuestions;
    private List<Answer> singleAnswers = new List<Answer>();

    Question question = new Question(-1);

    public static bool isSinglePlayer;

    private int index;

    private void Start()
    {
        if (singleQuestions == null)
            ApiGetQuestion(Answer.None);
        else
        {

            index = 0;
            MenuUpdate(singleQuestions[index]);
            singleQuestions.Clear();
            startTime = GameManager.Timer;
            
        }
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

        if (isSinglePlayer)
        {
            index++;
            singleAnswers.Add(StringToAnswer(btn.name));

            if (singleQuestions.Count > index)
                MenuUpdate(singleQuestions[index]);
            else
            {

                //simdiki zamandan oyun bitinceki zamaný çýkartýp sorularu çözdügü total süreyi buluyoruz.
                int finishTime = GameManager.Timer - startTime;

                //puan hesaplama.

                int point = 0;

                for (int i = 0; i < singleQuestions.Count; i++)
                {

                    if (singleQuestions[i].currentAnswer == AnswerToString(singleAnswers[i]))
                        point += 10;
                    else
                        point -= 3;

                }

                Debug.Log(point + " / " + finishTime + " - " + GameManager.Timer);

                // point 0 ýn altýna hiçbir zaman inmemli
                if (point < 0)
                    point = 0;

                 
                //Single Player Ekrana Verileri Yazma
                singleFinishPanel.SetActive(true);

                FinishDataWrite data = singleData.GetComponent<FinishDataWrite>();

                Debug.Log(point + " / " + finishTime + " - " + GameManager.Timer);

                data.CreateDates("Game Over",
                    new FinishDataWrite.Data("Point : ", point.ToString()),
                    new FinishDataWrite.Data("Finish Time : ", finishTime.ToString()));

                //apiye veriler veriliyor.
                ApiConnection.Connection<SingleGetApiData>("singlePlayer.php", new SingleGetApiData(GameManager.Token, point), (value) =>
                {

                });

                singleQuestions = null;
                isSinglePlayer = false;
            }

        }
        else
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
            finish.enemyUsername,
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

    public static Answer StringToAnswer(string str)
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

    public static string AnswerToString(Answer answer)
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
