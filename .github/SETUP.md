# GitHub Actions Deployment Setup

This guide explains how to set up the GitHub Actions workflow for deploying the SteveShop application to your VPS.

## Setting up GitHub Secrets

To deploy securely, you need to add the following secrets to your GitHub repository:

1. Go to your GitHub repository
2. Click on "Settings" > "Secrets and variables" > "Actions"
3. Click on "New repository secret"
4. Add the following secrets:

| Secret Name | Description | Example Value |
|-------------|-------------|---------------|
| `VPS_HOST` | Your VPS IP address | 203.161.47.238 |
| `VPS_PORT` | SSH port for your VPS | 22022 |
| `VPS_USERNAME` | SSH username | root |
| `VPS_PASSWORD` | SSH password | (your secure password) |
| `REPO_URL` | The URL of your Git repository | https://github.com/yourusername/steveshop.git |

## Security Best Practices

1. **Use SSH Keys Instead of Passwords**: For better security, consider using SSH keys instead of passwords. You can modify the workflow to use `ssh_key` instead of `password`.

2. **Use a Dedicated Deployment User**: Create a dedicated user with limited permissions for deployments rather than using root.

3. **Set Up HTTPS**: Make sure your site is served over HTTPS.

## Testing the Workflow

Once you've set up the secrets and pushed the workflow file to your repository, the workflow will run automatically on every push to the main branch.

You can monitor the workflow execution in the "Actions" tab of your GitHub repository.

## Customization

Feel free to customize the workflow file (`.github/workflows/deploy.yml`) to match your specific deployment requirements. 