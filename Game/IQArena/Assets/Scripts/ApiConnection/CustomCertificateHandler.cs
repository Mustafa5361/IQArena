using UnityEngine.Networking;
using System.Net.Security;
using System.Security.Cryptography.X509Certificates;

public class CustomCertificateHandler : CertificateHandler
{
    protected override bool ValidateCertificate(byte[] certificateData)
    {
        return true;
    }
}