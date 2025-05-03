<?php
require_once '../../../vendor/autoload.php'; // Certifique-se de que o autoload do Composer está incluído

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
class EmailSender {

    private $mail;
    private $smtpHost; // Host SMTP padrão
    private $smtpPort; // Porta padrão para SMTP
    private $smtpUsername; // Usuário do e-mail que envia o e-mail
    private $smtpPassword ; // Senha do e-mail ou token de autenticação
    private $isSMTPEnabled = false;

    public function __construct(string $smtpHost = '', int $smtpPort = 587, string $smtpUsername = '', string $smtpPassword = '') {
        $this->mail = new PHPMailer(true); // 'true' habilita tratamento de exceções
        if (!empty($smtpHost) && !empty($smtpUsername) && !empty($smtpPassword)) {
            $this->isSMTPEnabled = true;
            $this->smtpHost = $smtpHost;
            $this->smtpPort = $smtpPort;
            $this->smtpUsername = $smtpUsername;
            $this->smtpPassword = $smtpPassword;
            $this->mail->CharSet = 'UTF-8'; // Define o charset para UTF-8
            $this->mail->Encoding = 'base64'; // Define a codificação para base64
        }
    }

    public function setFrom(string $email, string $name = ''): void {
        $this->mail->setFrom($email, $name);
    }

    public function addRecipient(string $email, string $name = ''): void {
        $this->mail->addAddress($email, $name);
    }

    public function addCC(string $email, string $name = ''): void {
        $this->mail->addCC($email, $name);
    }

    public function addBCC(string $email, string $name = ''): void {
        $this->mail->addBCC($email, $name);
    }

    public function setSubject(string $subject): void {
        $this->mail->Subject = $subject;
    }

    public function setBody(string $body, bool $isHTML = false): void {
        if ($isHTML) {
            $this->mail->isHTML(true);
            $this->mail->Body = $body;
        } else {
            $this->mail->Body = $body;
        }
    }

    public function addAttachment(string $path, string $name = '', string $encoding = 'base64', string $type = '', string $disposition = 'attachment'): void {
        $this->mail->addAttachment($path, $name, $encoding, $type, $disposition);
    }

    public function send(): bool {
        try {
            if ($this->isSMTPEnabled) {
                $this->mail->isSMTP();
                $this->mail->Host = $this->smtpHost;
                $this->mail->SMTPAuth = true;
                $this->mail->Username = $this->smtpUsername;
                $this->mail->Password = $this->smtpPassword;
                $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // ou PHPMailer::ENCRYPTION_STARTTLS
                $this->mail->Port = $this->smtpPort;
                $this->mail->CharSet = 'UTF-8';
            }

            $this->mail->send();
            return true;
        } catch (Exception $e) {
            echo "Erro ao enviar o e-mail: {$this->mail->ErrorInfo}";
            return false;
        }
    }
}

// Exemplo de uso:

// Para usar com SMTP (recomendado para produção):
// $mailSenderSMTP = new EmailSender('smtp.gmail.com', 465, 'medcartransportemedico@gmail.com', 'jren boiu nvpp gkav');
$mailSenderSMTP->setFrom('medcartransportemedico@gmail.com', 'MedCar Transporte Médico');
$mailSenderSMTP->addRecipient('gregoryminho@gmail.com', 'gregory');
$mailSenderSMTP->addCC('lucaspraxedes74@gmail.com'); // Adicione CC se necessário
$mailSenderSMTP->addBCC('augusto10barros@gmail.com'); // Adicione BCC se necessário
$mailSenderSMTP->setSubject('MEDCAR - teste de envio de e-mail com SMTP');
$mailSenderSMTP->setBody('lorem ipsun dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.');
// $mailSenderSMTP->setBody('Este é o <b>corpo</b> do e-mail em <i>HTML</i>.', true);
// $mailSenderSMTP->addAttachment('/caminho/para/seu/arquivo.pdf', 'nome_do_arquivo.pdf');

if ($mailSenderSMTP->send()) {
    echo "E-mail enviado com sucesso!";
} else {
    echo "Falha ao enviar o e-mail.";
}

echo "\n\n";


?>