using System.Collections.Generic;
using UnityEngine;

public class CreateBtns : MonoBehaviour
{

    private void Awake()
    {
        
        

    }

    #region Unit Buttunlarýnýn oluþturulmasý.

    [SerializeField] private GameObject unitBtnSpawnPoint; // oluþturulacaklarý panel
    [SerializeField] private GameObject unitBtnPrefab;

    List<GameObject> unitBtns = new List<GameObject>();

    public void CreateButtonsUnit(params UnitBtnData[] unitData)
    {
        
        foreach(var unitBtn in unitBtns)
        {

            Destroy(unitBtn);

        }

        unitBtns.Clear();

        int height = 0;

        foreach (UnitBtnData data in unitData)
        {

            GameObject go = Instantiate(unitBtnPrefab, unitBtnSpawnPoint.transform);

            go.GetComponent<UnitBtn>().SetData(data);

            unitBtns.Add(go);

            height += (int)go.GetComponent<RectTransform>().sizeDelta.y + 10;

        }

        unitBtnSpawnPoint.GetComponent<RectTransform>().sizeDelta = new Vector2(0, height);

    }

    #endregion

    #region Unit Buttunlarýnýn oluþturulmasý.

    [SerializeField] private GameObject questionBtnSpawnPoint;
    [SerializeField] private GameObject questionBtnPrefab;
    
    List<GameObject> questionBtns = new List<GameObject>();

    public void CreateButtonsQuestion(params QuestionBtnData[] questionData)
    {

        foreach (var questionBtn in questionBtns)
        {

            Destroy(questionBtn);

        }

        questionBtns.Clear();

        int height = 0;

        foreach (QuestionBtnData data in questionData)
        {

            GameObject go = Instantiate(questionBtnPrefab, questionBtnSpawnPoint.transform);

            go.GetComponent<QuestionBtn>().SetData(data);

            unitBtns.Add(go);

            height += (int)go.GetComponent<RectTransform>().sizeDelta.y + 10;

        }

        questionBtnSpawnPoint.GetComponent<RectTransform>().sizeDelta = new Vector2(0, height);

    }

    #endregion

}
