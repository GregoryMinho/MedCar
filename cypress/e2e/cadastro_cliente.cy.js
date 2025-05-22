/// <reference types="cypress" />

describe('Testes E2E - Cadastro de Cliente', () => {
  const baseUrl = 'http://localhost/MedQ-2/paginas/cadastro_cliente.php'
  const successUrl = '/MedQ-2/area_clientes/menu_principal.php'
  const loginUrl = '/MedQ-2/paginas/login_clientes.php'

  beforeEach(() => {
    cy.visit(baseUrl)
    // Ignora erros específicos do jQuery se necessário
    Cypress.on('uncaught:exception', () => false)
  })

  it('1. Deve carregar a página corretamente', () => {
    cy.get('nav').should('be.visible')
    cy.get('h2').contains('Cadastre-se').should('be.visible')
    cy.get('form#cadastro-form').should('exist')
    cy.get('a[href*="login_clientes"]').should('exist')
  })

  it('2. Deve exibir menu mobile corretamente', () => {
    cy.viewport('iphone-6')
    cy.get('#mobile-menu-button').click()
    cy.get('#mobile-menu').should('be.visible')
    cy.get('#close-menu-button').click()
    cy.get('#mobile-menu').should('not.be.visible')
  })

  it('3. Deve aplicar máscaras corretamente', () => {
    cy.get('#cpf')
      .type('12345678901')
      .should('have.value', '123.456.789-01')

    cy.get('#telefone')
      .type('11987654321')
      .should('have.value', '(11) 98765-4321')

    cy.get('#contato_emergencia')
      .type('11987654321')
      .should('have.value', '(11) 98765-4321')

    cy.get('#cep')
      .type('01234000')
      .should('have.value', '01234-000')
  })

  it('4. Deve validar campos obrigatórios', () => {
    cy.get('form#cadastro-form').submit()
    
    // Verifica mensagens de erro para campos obrigatórios
    cy.get('#nome:invalid').should('exist')
    cy.get('#email:invalid').should('exist')
    cy.get('#cpf:invalid').should('exist')
    cy.get('#data_nascimento:invalid').should('exist')
    cy.get('#senha:invalid').should('exist')
  })

  it('5. Deve validar formato de email', () => {
    cy.get('#email').type('email-invalido')
    cy.get('form#cadastro-form').submit()
    cy.get('#email:invalid').should('exist')
  })

  it('6. Deve validar data de nascimento', () => {
    const futureDate = new Date()
    futureDate.setFullYear(futureDate.getFullYear() + 1)
    const futureDateStr = futureDate.toISOString().split('T')[0]

    cy.get('#data_nascimento').type(futureDateStr)
    cy.get('form#cadastro-form').submit()
    cy.get('#data_nascimento:invalid').should('exist')
  })

  it('7. Deve mostrar erro quando senhas não coincidem', () => {
    cy.window().then((win) => {
      cy.stub(win, 'alert').as('alertStub')
    })

    cy.get('#senha').type('Senha123@')
    cy.get('#confirmar_senha').type('Senha456@')

    cy.get('form#cadastro-form').submit()
    cy.get('@alertStub').should('have.been.calledWith', 'As senhas não coincidem!')
  })

  it('8. Deve enviar formulário com dados válidos', () => {
    const testData = {
      nome: 'Cliente Teste',
      email: `cliente_${Date.now()}@teste.com`,
      cpf: '123.456.789-09',
      telefone: '(11) 98765-4321',
      data_nascimento: '1990-01-01',
      contato_emergencia: '(11) 91234-5678',
      rua: 'Rua Teste',
      numero: '123',
      bairro: 'Centro',
      cidade: 'São Paulo',
      estado: 'SP',
      cep: '01234-000',
      senha: 'SenhaSegura123@'
    }

    // Mock da resposta do servidor
    cy.intercept('POST', '/MedQ-2/paginas/actions/action_cadastro_cliente.php', {
      statusCode: 302,
      headers: {
        location: successUrl
      }
    }).as('submitForm')

    // Preenche todos os campos
    cy.get('#nome').type(testData.nome)
    cy.get('#email').type(testData.email)
    cy.get('#cpf').type(testData.cpf)
    cy.get('#telefone').type(testData.telefone)
    cy.get('#data_nascimento').type(testData.data_nascimento)
    cy.get('#contato_emergencia').type(testData.contato_emergencia)
    cy.get('#rua').type(testData.rua)
    cy.get('#numero').type(testData.numero)
    cy.get('#bairro').type(testData.bairro)
    cy.get('#cidade').type(testData.cidade)
    cy.get('#estado').type(testData.estado)
    cy.get('#cep').type(testData.cep)
    cy.get('#senha').type(testData.senha)
    cy.get('#confirmar_senha').type(testData.senha)

    cy.get('form#cadastro-form').submit()
    
    cy.wait('@submitForm').then((interception) => {
      expect(interception.request.body).to.include(`email=${encodeURIComponent(testData.email)}`)
      expect(interception.request.body).to.include(`cpf=${testData.cpf.replace(/\D/g, '')}`)
    })

    // Verifica se foi redirecionado
    cy.url().should('include', successUrl)
  })

  it('9. Deve redirecionar para login ao clicar no link', () => {
    cy.get(`a[href="${loginUrl}"]`).click()
    cy.url().should('include', loginUrl)
  })

  it('10. Deve exibir mensagem de erro da sessão', () => {
    // Visita a página com parâmetro de erro
    cy.visit(`${baseUrl}?erro=Mensagem de erro teste`)
    cy.contains('Mensagem de erro teste').should('be.visible')
  })

  it('11. Deve preencher email automaticamente quando fornecido via URL', () => {
    const testEmail = 'teste@exemplo.com'
    cy.visit(`${baseUrl}?email=${btoa(testEmail)}`)
    cy.get('#email').should('have.value', testEmail)
  })

  it('12. Deve desativar botão de submit durante o envio', () => {
    // Mock de resposta lenta para testar estado do botão
    cy.intercept('POST', '/MedQ-2/paginas/actions/action_cadastro_cliente.php', {
      delay: 2000,
      statusCode: 302,
      headers: { location: successUrl }
    }).as('slowSubmit')

    // Preenche dados mínimos
    cy.get('#nome').type('Teste')
    cy.get('#email').type('teste@exemplo.com')
    cy.get('#cpf').type('123.456.789-09')
    cy.get('#senha').type('Senha123@')
    cy.get('#confirmar_senha').type('Senha123@')

    cy.get('form#cadastro-form').submit()
    cy.get('#submit-button').should('be.disabled')
    cy.get('#submit-button').contains('Aguarde...')

    cy.wait('@slowSubmit')
  })
})