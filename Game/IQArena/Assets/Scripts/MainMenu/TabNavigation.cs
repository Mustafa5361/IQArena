using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.EventSystems;
using UnityEngine.UI;

public class TabNavigation : MonoBehaviour
{

    void Update()
    {
        if (Input.GetKeyDown(KeyCode.Tab))
        {

            GameObject current = EventSystem.current.currentSelectedGameObject;

            if (current != null)
            {
                Selectable currentSelectable = current.GetComponent<Selectable>();

                if (currentSelectable != null)
                {
                    Selectable next = Input.GetKey(KeyCode.LeftShift) || Input.GetKey(KeyCode.RightShift)
                        ? currentSelectable.FindSelectableOnUp()
                        : currentSelectable.FindSelectableOnDown();

                    if (next != null)
                        next.Select();
                }

            }

        }

    }

}
