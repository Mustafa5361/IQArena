public class User
{

    public string mail;
    public string username;
    public string password;

    public User(string mail, string username, string password)
    {
        this.mail = mail;
        this.username = username;
        this.password = password;
    }

    public override string ToString()
    {
        return mail + "/" + username + "/" + password; 
    }

}

public class LoginGetData
{

    public bool success;
    public string massage;

}