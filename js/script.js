/**
 * =========================================
 * 1. INICIALIZAÇÃO GERAL
 * =========================================
 */
document.addEventListener('DOMContentLoaded', function() {
    
    // --- MENU MOBILE ---
    const btnMobile = document.getElementById('mobile-btn');
    const menuLista = document.getElementById('menu-lista'); // Seleciona a lista UL

    if (btnMobile && menuLista) {
        // Alternar menu ao clicar no botão
        btnMobile.addEventListener('click', function() {
            menuLista.classList.toggle('active');
        });

        // Fechar menu ao clicar em qualquer link (para navegar na mesma página)
        const links = menuLista.querySelectorAll('a');
        links.forEach(link => {
            link.addEventListener('click', () => {
                menuLista.classList.remove('active');
            });
        });
    }

    // --- CARROSSEL COM SPLIDE.JS ---
    if (typeof Splide !== 'undefined') {
        const elms = document.getElementsByClassName('splide');

        // Inicializa todos os carrosséis encontrados na página
        for (let i = 0; i < elms.length; i++) {
            new Splide(elms[i], {
                type: 'loop',       // Loop infinito
                perPage: 3,         // 3 cards no Desktop
                perMove: 1,
                gap: '30px',
                pagination: false,
                arrows: true,
                
                // Responsividade
                breakpoints: {
                    1024: {
                        perPage: 2, // 2 cards em Tablets
                    },
                    768: {
                        perPage: 1, // 1 card em Celulares
                        gap: '15px',
                        padding: '0' // Sem padding lateral: foca no card inteiro (CSS controla a imagem)
                    }
                }
            }).mount();
        }
    } else {
        console.warn("A biblioteca Splide.js não foi encontrada.");
    }

    // --- INICIALIZA O MODAL DE SERVIÇOS ---
    initModal();
});

/**
 * =========================================
 * 2. LÓGICA DO MODAL (SERVIÇOS)
 * =========================================
 */
function initModal() {
    const modal = document.getElementById('modal-curso');
    const closeBtn = document.querySelector('.close-modal');
    
    // Elementos Internos do Modal
    const modalTitle = document.getElementById('modal-titulo');
    const modalDesc = document.getElementById('modal-descricao');
    const modalWhatsapp = document.getElementById('modal-whatsapp-btn');
    
    // Delegação de eventos para capturar cliques nos botões "Saiba Mais"
    document.addEventListener('click', (e) => {
        // Verifica se clicou num botão de abrir modal
        const btn = e.target.closest('.btn-abrir-modal');
        
        if (!btn) return; 

        e.preventDefault();

        // Encontra o card pai para extrair os dados
        const card = btn.closest('.card-curso');
        
        // Extrai dados do serviço
        const titulo = card.querySelector('h3').innerText;
        // Pega a descrição do atributo data (mais confiável) ou do texto do card
        const descricao = card.getAttribute('data-descricao') || card.querySelector('p').innerText;

        // Preenche o Modal
        modalTitle.innerText = titulo;
        modalDesc.innerText = descricao;

        // Link do WhatsApp para Orçamento
        // Número da Loja (Arroio dos Ratos)
        const telefoneDestino = "5551980402554"; 
        const mensagem = encodeURIComponent(`Olá! Gostaria de solicitar um orçamento para o serviço de: ${titulo}.`);
        
        modalWhatsapp.href = `https://wa.me/${telefoneDestino}?text=${mensagem}`;

        // Exibe o modal
        modal.classList.add('active');
    });

    // Função para fechar o modal
    const fecharModal = () => {
        modal.classList.remove('active');
    };

    if (closeBtn) closeBtn.addEventListener('click', fecharModal);

    // Fecha ao clicar no fundo escuro
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            fecharModal();
        }
    });
}