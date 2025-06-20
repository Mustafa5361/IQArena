using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.UI;

public class UnitBtn : MonoBehaviour
{

    public Text titleTxt;
    public Toggle visibilityToggel;
    public Toggle accessibilityToggel;

    public void SetData(UnitBtnData data)
    {

        this.titleTxt.text = data.unitName;
        this.visibilityToggel.isOn = data.visibility;
        this.accessibilityToggel.isOn = data.accessibility;

    }

}

[System.Serializable]
public class UnitBtnData
{

    public int unitID;
    public string unitName;
    public bool visibility;
    public bool accessibility;

    public UnitBtnData() { }

    public UnitBtnData(int unitID, string title, bool visibility, bool accessibility)
    {

        this.unitID = unitID;
        this.unitName = title;
        this.visibility = visibility;
        this.accessibility = accessibility;

    }

}

[System.Serializable]
public class GetUnitBtnData
{

    public List<UnitBtnData> units;

}
