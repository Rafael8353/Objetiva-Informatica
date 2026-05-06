# 💻 Sistema de Gestão e Captação - Assistência Técnica

Uma plataforma web dinâmica desenvolvida para uma loja de informática e assistência técnica. O sistema combina uma vitrine de serviços interativa com captação direta de orçamentos via WhatsApp e E-mail, além de um painel administrativo seguro para gerenciamento de portfólio.

## 🚀 Visão Geral do Projeto

Este projeto é a evolução de uma landing page estática criada por mim, o site antigamente estático agora é totalmente dinâmico, o que era apenas uma landing page comum agora é possível ser editada por um administrador da empresa sem que ele tenha conhecimentos de programação graças a tela de gestão. Ele permite que os gestores da loja atualizem os serviços oferecidos em tempo real através de um painel de controle próprio, além de rotear os pedidos de orçamento diretamente para o e-mail oficial da loja via SMTP autenticado.

## ✨ Funcionalidades

### Frontend (Vitrine da Loja)
* **Catálogo Dinâmico:** Carrossel de serviços responsivo construído com a biblioteca Splide.js, alimentado em tempo real pelo banco de dados.
* **Modais de Detalhamento:** Sistema de cards flutuantes que exibem a descrição expandida de cada serviço ao clicar em "Saiba Mais".
* **Orçamento via WhatsApp:** Geração de links dinâmicos que direcionam o cliente para o WhatsApp da loja com mensagens pré-preenchidas com o serviço de interesse.
* **Formulário de Contato Nativo:** Captação de leads processada no próprio servidor com feedback visual assíncrono (sem recarregar a página), eliminando a dependência do Formspree.

### Backend (Painel Administrativo)
* **Autenticação Segura:** Acesso restrito via login e senha.
* **CRUD de Serviços:** Interface amigável para Cadastrar, Ler, Atualizar e Excluir serviços (incluindo título, descrições curtas/longas e upload de imagens).
* **API RESTful:** Endpoint leve (`api-servicos.php`) que distribui os dados em JSON para o frontend.
* **Disparo de E-mails (SMTP):** Integração com PHPMailer garantindo entrega autenticada (SSL/TLS) na caixa de entrada da loja, com proteção contra injeção de código (`htmlspecialchars`).
* **Auto-Setup de Banco de Dados:** Criação automática da tabela `servicos` no primeiro acesso ao painel.

## 🛠️ Tecnologias Utilizadas

* **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
* **Backend:** PHP (Vanilla)
* **Banco de Dados:** MySQL / PDO (PHP Data Objects)
* **Envio de E-mails:** [PHPMailer](https://github.com/PHPMailer/PHPMailer)
* **Componentes UI:** [Splide.js](https://splidejs.com/)

## 📁 Estrutura de Diretórios

```text
/
├── index.html                 # Landing page e vitrine de serviços
├── gestao-assistencia.php     # Painel Administrativo de Serviços
├── api-servicos.php           # Endpoint JSON do catálogo
├── enviar-email.php           # Controlador de envio de orçamentos (SMTP)
├── css/                       # Folhas de estilo (tema Roxo/Azul)
├── js/                        # Scripts de interação e consumo de API
├── img/                       # Uploads de imagens e assets da marca
└── PHPMailer/                 # Motor de envio de e-mails