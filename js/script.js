document.addEventListener('DOMContentLoaded', function() {
    
    // --- LÓGICA DE ALERTA DO FORMULÁRIO (SUCESSO/ERRO) ---
    const urlParams = new URLSearchParams(window.location.search);
    const statusForm = urlParams.get('status');

    if (statusForm === 'sucesso') {
        const msgSucesso = document.getElementById('msg-sucesso');
        if (msgSucesso) {
            msgSucesso.style.display = 'block'; 
            const novaUrl = window.location.pathname + window.location.hash;
            window.history.replaceState({}, document.title, novaUrl);
            setTimeout(() => { msgSucesso.scrollIntoView({ behavior: 'smooth', block: 'center' }); }, 500);
        }
    } else if (statusForm === 'erro') {
        const msgErro = document.getElementById('msg-erro');
        if (msgErro) {
            msgErro.style.display = 'block';
            msgErro.innerText = 'Ocorreu um erro ao enviar. Tente novamente ou chame no WhatsApp.';
            const novaUrl = window.location.pathname + window.location.hash;
            window.history.replaceState({}, document.title, novaUrl);
            setTimeout(() => { msgErro.scrollIntoView({ behavior: 'smooth', block: 'center' }); }, 500);
        }
    }

    // --- MENU MOBILE ---
    const btnMobile = document.getElementById('mobile-btn');
    const menuLista = document.getElementById('menu-lista');

    if (btnMobile && menuLista) {
        btnMobile.addEventListener('click', function() {
            menuLista.classList.toggle('active');
        });
        const links = menuLista.querySelectorAll('a');
        links.forEach(link => {
            link.addEventListener('click', () => {
                menuLista.classList.remove('active');
            });
        });
    }

    // --- CARROSSEL DINÂMICO E SPLIDE ---
    if (typeof Splide !== 'undefined') {
        
        // Função para montar o HTML do card
        function criarCard(servico) {
            // A descrição curta vai no <p> e a longa vai no data-descricao pro modal
            return `
                <li class="splide__slide">
                    <div class="card-curso" data-descricao="${servico.descricao_longa || ''}">
                        <div class="card-img-top btn-abrir-modal" style="cursor: pointer;">
                            <img src="${servico.imagem}" alt="${servico.titulo}">
                        </div>
                        <div class="card-body">
                            <h3>${servico.titulo}</h3>
                            <p>${servico.descricao_curta || ''}</p>
                            <a href="#" class="btn-link btn-abrir-modal">Saiba Mais →</a>
                        </div>
                    </div>
                </li>
            `;
        }

        // Busca os dados no Banco
        fetch('api-servicos.php')
            .then(response => response.json())
            .then(servicos => {
                const listaServicos = document.getElementById('lista-servicos');

                if(servicos.erro) { console.error('Erro no Banco:', servicos.erro); return; }

                // Injeta os cards na tela
                servicos.forEach(servico => {
                    if (listaServicos) listaServicos.innerHTML += criarCard(servico);
                });

                // Só inicia o carrossel DEPOIS que os cards foram criados
                const elms = document.getElementsByClassName('splide');
                for (let i = 0; i < elms.length; i++) {
                    if (elms[i].querySelector('.splide__slide')) {
                        new Splide(elms[i], {
                            type: 'loop',
                            perPage: 3,
                            perMove: 1,
                            gap: '30px',
                            pagination: false,
                            arrows: true,
                            breakpoints: {
                                1024: { perPage: 2 },
                                768: { perPage: 1, gap: '15px', padding: '0' }
                            }
                        }).mount();
                    }
                }
            })
            .catch(error => console.error('Erro ao conectar com a API:', error));
            
    }

    initModal();
});

function initModal() {
    const modal = document.getElementById('modal-curso');
    const closeBtn = document.querySelector('.close-modal');
    const modalTitle = document.getElementById('modal-titulo');
    const modalDesc = document.getElementById('modal-descricao');
    const modalWhatsapp = document.getElementById('modal-whatsapp-btn');
    
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-abrir-modal');
        if (!btn) return; 

        e.preventDefault();

        const card = btn.closest('.card-curso');
        const titulo = card.querySelector('h3').innerText;
        const descricao = card.getAttribute('data-descricao') || card.querySelector('p').innerText;

        modalTitle.innerText = titulo;
        modalDesc.innerText = descricao;

        const telefoneDestino = "5551980402554"; 
        const mensagem = encodeURIComponent(`Olá! Gostaria de solicitar um orçamento para: ${titulo}.`);
        modalWhatsapp.href = `https://wa.me/${telefoneDestino}?text=${mensagem}`;

        modal.classList.add('active');
    });

    const fecharModal = () => { modal.classList.remove('active'); };
    if (closeBtn) closeBtn.addEventListener('click', fecharModal);
    window.addEventListener('click', (e) => { if (e.target === modal) fecharModal(); });
}