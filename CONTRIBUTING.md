# Contributing to Bank Sampah Faperta

Thank you for your interest in contributing! This guide will help you get started.

---

## 🤝 Ways to Contribute

- **Bug Reports:** Submit detailed bug reports with reproduction steps
- **Feature Requests:** Propose new features with use cases
- **Code Contributions:** Submit pull requests for bug fixes or features
- **Documentation:** Improve or add documentation
- **Testing:** Write tests or improve test coverage
- **Code Review:** Review pull requests from other contributors

---

## 📋 Before You Start

1. **Check Existing Issues:** Search for existing issues before creating new ones
2. **Discuss Major Changes:** Open an issue to discuss significant changes before coding
3. **Read Documentation:** Familiarize yourself with the codebase structure
4. **Follow Code Standards:** Adhere to the project's coding conventions

---

## 🚀 Getting Started

### 1. Fork and Clone

```bash
# Fork the repository on GitHub, then:
git clone https://github.com/YOUR_USERNAME/banksampah-faperta.git
cd banksampah-faperta

# Add upstream remote
git remote add upstream https://github.com/jey41/banksampah-faperta.git
```

### 2. Set Up Development Environment

```bash
# Install dependencies
composer install
npm install

# Configure environment
cp .env.example .env
php artisan key:generate

# Setup database
touch database/database.sqlite
php artisan migrate --seed

# Start development server
composer run dev
```

### 3. Create a Branch

```bash
# Update your fork
git checkout main
git pull upstream main

# Create feature branch
git checkout -b feature/your-feature-name
# or for bug fixes
git checkout -b bugfix/issue-description
```

---

## 💻 Development Workflow

### Making Changes

1. **Write Clean Code:** Follow PSR-12 for PHP, ESLint rules for JavaScript
2. **Add Tests:** Write tests for new features or bug fixes
3. **Update Documentation:** Update relevant docs when changing functionality
4. **Commit Regularly:** Make atomic commits with clear messages

### Running Tests

```bash
# Run PHP tests
php artisan test

# Run specific test file
php artisan test tests/Feature/DepositTest.php

# Run with coverage
php artisan test --coverage
```

### Code Quality Checks

```bash
# Format PHP code
./vendor/bin/pint

# Check JavaScript formatting
npm run lint
```

---

## 📝 Commit Message Guidelines

Follow **Conventional Commits** format:

```
<type>(<scope>): <subject>

<body>

<footer>
```

### Types

- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, no logic change)
- `refactor`: Code refactoring
- `test`: Adding or updating tests
- `chore`: Maintenance tasks (dependencies, build config)
- `perf`: Performance improvements

### Examples

```bash
# Feature
feat(deposits): add bulk deposit import functionality

# Bug fix
fix(withdrawals): correct balance calculation for concurrent requests

# Documentation
docs(api): add endpoint documentation for pickup requests

# Refactor
refactor(controllers): extract deposit logic to service class

# Test
test(deposits): add tests for donation deposits
```

### Best Practices

- Use present tense ("add feature" not "added feature")
- Capitalize first letter of subject
- No period at the end of subject
- Keep subject line under 72 characters
- Separate subject from body with blank line
- Explain what and why, not how (code shows how)

---

## 🔀 Pull Request Process

### 1. Prepare Your PR

```bash
# Ensure your branch is up to date
git checkout main
git pull upstream main
git checkout feature/your-feature
git rebase main

# Push to your fork
git push origin feature/your-feature
```

### 2. Create Pull Request

- Go to your fork on GitHub
- Click "New Pull Request"
- Select `jey41/banksampah-faperta:main` as base
- Select your feature branch as compare

### 3. PR Title and Description

**Title Format:**
```
feat: add user badge notification system
```

**Description Template:**
```markdown
## Description
Brief description of what this PR does.

## Changes
- Added X feature
- Fixed Y bug
- Updated Z documentation

## Testing
- [ ] All tests pass
- [ ] Added new tests for new features
- [ ] Manual testing completed

## Screenshots (if applicable)
[Add screenshots for UI changes]

## Related Issues
Closes #123
Related to #456

## Checklist
- [ ] Code follows project style guidelines
- [ ] Self-review completed
- [ ] Documentation updated
- [ ] No breaking changes (or documented if unavoidable)
```

### 4. Code Review Process

- Address reviewer feedback promptly
- Push new commits for requested changes
- Mark conversations as resolved when addressed
- Be respectful and professional in discussions

### 5. After Approval

- Maintainers will merge your PR
- Your branch will be deleted automatically
- Pull latest main to your local repo

---

## 🐛 Bug Reports

### Before Reporting

- Check if the bug is already reported
- Try to reproduce on latest version
- Test in a clean environment if possible

### Bug Report Template

```markdown
**Describe the bug**
A clear description of what the bug is.

**To Reproduce**
Steps to reproduce:
1. Go to '...'
2. Click on '...'
3. See error

**Expected behavior**
What you expected to happen.

**Actual behavior**
What actually happened.

**Screenshots**
If applicable, add screenshots.

**Environment:**
- OS: [e.g. Ubuntu 22.04]
- PHP Version: [e.g. 8.3]
- Laravel Version: [e.g. 11.0]
- Browser: [e.g. Chrome 120]

**Additional context**
Any other relevant information.
```

---

## 💡 Feature Requests

### Feature Request Template

```markdown
**Is your feature request related to a problem?**
A clear description of the problem. Ex. I'm frustrated when [...]

**Describe the solution you'd like**
Clear description of what you want to happen.

**Describe alternatives you've considered**
Alternative solutions or features you've considered.

**Use cases**
How would this feature be used? Who would benefit?

**Additional context**
Mockups, examples, or other context.
```

---

## 📐 Coding Standards

### PHP/Laravel

- Follow **PSR-12** coding standard
- Use **Laravel Pint** for automatic formatting: `./vendor/bin/pint`
- Type-hint method parameters and return types
- Use Eloquent relationships over manual queries
- Use dependency injection over facades where appropriate

**Good:**
```php
public function store(StoreDepositRequest $request, DepositService $service): RedirectResponse
{
    $deposit = $service->createDeposit($request->validated());
    
    return redirect()
        ->route('nasabah.dashboard')
        ->with('success', 'Deposit created successfully.');
}
```

**Avoid:**
```php
public function store(Request $request)
{
    $deposit = Deposit::create($request->all()); // No validation
    return back(); // Unclear destination
}
```

### JavaScript/React

- Use **ESLint** and **Prettier**
- Prefer functional components over class components
- Use hooks appropriately
- Keep components focused (Single Responsibility)
- Extract reusable logic to custom hooks

**Good:**
```jsx
export default function DepositCard({ deposit, onApprove }) {
    const [isLoading, setIsLoading] = useState(false);
    
    const handleApprove = async () => {
        setIsLoading(true);
        try {
            await onApprove(deposit.id);
        } finally {
            setIsLoading(false);
        }
    };
    
    return (
        <div className="card">
            <h3>{deposit.user.name}</h3>
            <button onClick={handleApprove} disabled={isLoading}>
                {isLoading ? 'Processing...' : 'Approve'}
            </button>
        </div>
    );
}
```

### Database Migrations

- Never edit existing migrations that have been deployed
- Create new migrations for schema changes
- Use descriptive migration names
- Always provide `down()` method for rollback

---

## ✅ Testing Guidelines

### Writing Tests

- Write tests for all new features
- Add tests when fixing bugs (to prevent regression)
- Aim for high test coverage (>80%)
- Use factories for test data

### Test Structure

```php
class DepositTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_nasabah_can_create_deposit(): void
    {
        // Arrange
        $nasabah = User::factory()->create(['role' => 'nasabah']);
        $trashPrice = TrashPrice::factory()->create();
        
        // Act
        $response = $this->actingAs($nasabah)->post(route('deposits.store'), [
            'items' => [
                ['trash_price_id' => $trashPrice->id, 'weight' => 5.0],
            ],
        ]);
        
        // Assert
        $response->assertRedirect();
        $this->assertDatabaseHas('deposits', [
            'user_id' => $nasabah->id,
        ]);
    }
}
```

---

## 🔒 Security Guidelines

### Reporting Security Vulnerabilities

**DO NOT** create public issues for security vulnerabilities.

Instead:
- Email security concerns to: [security@banksampah-faperta.com]
- Include detailed description and reproduction steps
- Allow time for fix before public disclosure

### Security Best Practices

- Never commit sensitive data (.env files, API keys, passwords)
- Always validate and sanitize user input
- Use Laravel's built-in security features (CSRF, SQL injection protection)
- Keep dependencies updated
- Follow OWASP Top 10 guidelines

---

## 📚 Documentation Standards

### Code Documentation

- Add PHPDoc blocks for classes and public methods
- Document complex logic with inline comments
- Keep comments up-to-date with code changes

```php
/**
 * Approve a pending deposit and update user balance.
 *
 * @param Deposit $deposit The deposit to approve
 * @return void
 * @throws InsufficientBalanceException
 */
public function approveDeposit(Deposit $deposit): void
{
    // Implementation
}
```

### Markdown Documentation

- Use clear headings and structure
- Include code examples where helpful
- Add table of contents for long documents
- Keep language clear and concise

---

## 💬 Communication

### Be Respectful

- Treat all contributors with respect
- Be constructive in feedback
- Assume positive intent
- Help newcomers feel welcome

### Asking Questions

- Check existing documentation first
- Search for similar questions in issues
- Provide context when asking
- Be patient waiting for responses

### Code Review Etiquette

**As Author:**
- Be open to feedback
- Don't take criticism personally
- Explain your reasoning when needed

**As Reviewer:**
- Be kind and constructive
- Explain why, not just what
- Suggest alternatives when possible
- Approve when ready, request changes when needed

---

## 🏆 Recognition

Contributors will be:
- Listed in CONTRIBUTORS.md
- Mentioned in release notes for significant contributions
- Given credit in commit history

---

## 📞 Getting Help

- **Documentation:** Check [PROJECT_OVERVIEW.md](../PROJECT_OVERVIEW.md) and [docs/](../docs/)
- **Issues:** Browse or create GitHub issues
- **Discussions:** Use GitHub Discussions for questions
- **Email:** Contact maintainers at [contact@banksampah-faperta.com]

---

## 📄 License

By contributing, you agree that your contributions will be licensed under the same license as the project (MIT License).

---

**Thank you for contributing to Bank Sampah Faperta! 🌱♻️**
