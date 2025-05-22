describe('Login da Empresa', () => {
  it('Deve fazer login com credenciais válidas', () => {
    cy.visit('http://localhost/MedQ-2/paginas/login_empresas.php');

    cy.get('input[name="email"]').type('speed@email.com');
    cy.get('input[name="password"]').type('$2a$10$Xp1Q4J9z7JQZJZJZJZJZJO');
    cy.get('form').submit();

    cy.url().should('include', '/MedQ-2/area_empresas/menu_principal.php');
  });

  it('Deve mostrar erro ao inserir senha errada', () => {
    cy.visit('http://localhost/MedQ-2/paginas/login_empresas.php');

    cy.get('input[name="email"]').type('empresa@exemplo.com');
    cy.get('input[name="password"]').type('senhaErrada');
    cy.get('form').submit();

    cy.url().should('include', '/MedQ-2/paginas/login_empresas.php');
    cy.contains('Senha ou E-mail incorreto').should('be.visible');
  });
});
