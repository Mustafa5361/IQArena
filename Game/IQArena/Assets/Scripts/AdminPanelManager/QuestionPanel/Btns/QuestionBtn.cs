using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.UI;

public class QuestionBtn : MonoBehaviour
{

    public Text titleTxt;
    public Toggle visibilityToggle;

    public void SetData(QuestionBtnData data)
    {

        this.titleTxt.text = data.question;
        this.visibilityToggle.isOn = data.isHide;

    }

}

[System.Serializable]
public class QuestionBtnData
{

    public int questionID;
    public string question;
    public bool isHide; // visibility

    public QuestionBtnData() { }

    public QuestionBtnData(int questionID, string question, bool visibility)
    {

        this.questionID = questionID;
        this.question = question;
        this.isHide = visibility;

    }

}

[System.Serializable]
public class GetQuestionBtnData
{

    public List<QuestionBtnData> questions;

}
