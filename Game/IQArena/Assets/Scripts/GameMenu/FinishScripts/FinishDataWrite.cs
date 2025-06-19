using System.Collections;
using System.Collections.Generic;
using TMPro;
using UnityEngine;

public class FinishDataWrite : MonoBehaviour
{

    [SerializeField] private TextMeshProUGUI title;

    [SerializeField] private GameObject dataField;
    [SerializeField] private GameObject data;

    public void CreateDates(string title, params Data[] datas)
    {

        this.title.text = title;

        foreach (var data in datas)
        {

            Instantiate(this.data, dataField.transform).AddComponent<Data>().SetData(data);
            

        }

    }

    public class Data : MonoBehaviour
    {

        private string title;

        public string Title
        {
            get { return title; }
            set
            {

                title = value;
                if (transform.GetChild(0).GetComponent<TextMeshProUGUI>() != null)
                {
                    transform.GetChild(0).GetComponent<TextMeshProUGUI>().text = title;
                }
                
            }
        }

        private string description;

        public string Description 
        { 
            get { return description; }
            set 
            {

                description = value;
                if (transform.GetChild(1).GetComponent<TextMeshProUGUI>() != null)
                {
                    transform.GetChild(1).GetComponent<TextMeshProUGUI>().text = description;
                }

            } 
        }

        public Data()
        {
            
        }

        public Data(string title, string description)
        {

            this.title = title;
            this.description = description;

        }

        public void SetData(Data data)
        {
            this.Title = data.Title;
            this.Description = data.Description;
        }

    }
}
