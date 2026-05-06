<?php
// Importa as classes do PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Carrega os arquivos do PHPMailer
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Captura os dados do formulário com trava de segurança (htmlspecialchars)
    $nome = htmlspecialchars($_POST['nome']);
    $email_cliente = htmlspecialchars($_POST['email']);
    $whatsapp = htmlspecialchars($_POST['whatsapp']);
    $mensagem_cliente = htmlspecialchars($_POST['mensagem']);

    $mail = new PHPMailer(true);

    try {
        // ==========================================
        // 1. CONFIGURAÇÕES DO SERVIDOR SMTP (LOCAWEB)
        // ==========================================
        $mail->isSMTP();                                            
        $mail->Host       = 'email-ssl.com.br'; 
        $mail->SMTPAuth   = true;                                   
        $mail->Username   = 'EMAIL DO SITE'; 
        $mail->Password   = 'SENHA DO EMAIL'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;         
        $mail->Port       = 465;                                 
        $mail->CharSet    = 'UTF-8';

        // ==========================================
        // 2. REMETENTE E DESTINATÁRIO
        // ==========================================
        // Quem está enviando (O robô)
        $mail->setFrom('EMAIL DO SITE', 'Site - Assistência Técnica'); 
        
        // Para qual e-mail vai chegar a mensagem (Caixa da Loja)
        $mail->addAddress('EMAIL DA DONA DA LOJA', 'DONA DA LOJA');     
        
        // Se a loja clicar em "Responder", vai pro e-mail do cliente
        $mail->addReplyTo($email_cliente, $nome);

        // ==========================================
        // 3. CONTEÚDO DO E-MAIL
        // ==========================================
        $mail->isHTML(true);                                  
        $mail->Subject = 'Novo Pedido de Orçamento - Assistência';
        
        // Corpo do e-mail em HTML (Bonito)
        $mail->Body    = "
            <h2>Novo Pedido de Orçamento</h2>
            <p><strong>Nome:</strong> {$nome}</p>
            <p><strong>E-mail:</strong> {$email_cliente}</p>
            <p><strong>WhatsApp:</strong> {$whatsapp}</p>
            <p><strong>Mensagem / Necessidade:</strong><br/>" . nl2br($mensagem_cliente) . "</p>
        ";
        
        // Corpo em texto puro (Segurança)
        $mail->AltBody = "Novo Pedido de Orçamento\nNome: {$nome}\nE-mail: {$email_cliente}\nWhatsApp: {$whatsapp}\nMensagem: {$mensagem_cliente}";

        // Envia o e-mail
        $mail->send();
        
        // Redireciona de volta com sucesso para a âncora certa
        header("Location: index.html?status=sucesso#orcamento");
        exit;
        
    } catch (Exception $e) {
        // Se der erro, redireciona mostrando que falhou
        header("Location: index.html?status=erro#orcamento");
        exit;
    }
} else {
    header("Location: index.html");
    exit;
}
?>