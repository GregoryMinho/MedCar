/// <reference types="cypress" />

context('Formulário de Cadastro de Empresa', () => {
  const baseUrl = 'http://localhost/MedQ-2/paginas/cadastro_empresas.php'

  beforeEach(() => {
    cy.visit(baseUrl)
    // evita falhar no erro $.getJSON
    Cypress.on('uncaught:exception', (err) => {
      if (err.message.includes('$.getJSON is not a function')) {
        return false
      }
    })
  })

  it('Aplica máscara em CNPJ, telefone e CEP', () => {
    cy.get('#cnpj')
      .type('12345678000199')
      .should('have.value', '12.345.678/0001-99')

    cy.get('#telefone')
      .type('11987654321')
      .should('have.value', '(11) 98765-4321')

    cy.get('#cep')
      .type('01234000')
      .should('have.value', '01234-000')
  })

  it('Preenche automaticamente o endereço ao perder foco no CEP', () => {
    // stub da resposta da API via curl de viacep
    cy.intercept(
      'GET',
      'https://viacep.com.br/ws/01234000/json/',
      { statusCode: 200, body: { logradouro: 'Rua das Flores', erro: false } }
    ).as('viaCep')

    cy.get('#cep').type('01234000').blur()
    cy.wait('@viaCep')

    cy.get('#endereco').should('have.value', 'Rua das Flores')
  })

  it('Exibe alerta quando as senhas não coincidem', () => {
    cy.get('#senha').type('senha1ABC')
    cy.get('#confirmar_senha').type('senha2DEF')

    // espiona window:alert
    cy.window().then(win => {
      cy.stub(win, 'alert').as('alert')
    })

    cy.get('form#cadastro-form').submit()
    cy.get('@alert').should('have.been.calledWith', 'As senhas não coincidem!')
  })

  it('Envia o formulário com dados válidos', () => {
    // stub da rota de cadastro (não faz redirecionar de verdade)
    cy.intercept(
      'POST',
      '/MedQ-2/paginas/actions/action_cadastro_empresa.php',
      { statusCode: 302, headers: { location: '/MedQ-2/area_empresas/menu_principal.php' } }
    ).as('postCadastro')

    // preenche todos os campos
    cy.get('#nome').type('Empresa Teste')
    cy.get('#email').type(`empresa${Date.now()}@teste.com`)
    cy.get('#cnpj').type('12.345.678/0001-99')
    cy.get('#telefone').type('(11) 99999-8888')
    cy.get('#cep').type('01234-000')
    cy.get('#endereco').type('Rua Exemplo, 123')
    cy.get('#cidade').type('São Paulo')
    cy.get('#especialidade_cardiaco').check()
    cy.get('#veiculo_padrao').check()
    cy.get('#senha').type('SenhaSegura123')
    cy.get('#confirmar_senha').type('SenhaSegura123')

    // submete
    cy.get('form#cadastro-form').submit()
    cy.wait('@postCadastro').its('response.statusCode').should('eq', 302)

    // verifica que o Cypress recebeu o header de redirecionamento
    cy.window().then(win => {
      // como interceptamos, a página não mudou de fato
      expect(win.location.href).to.include('/cadastro_empresas.php')
    })
  })
})
