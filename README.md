# Laravel Google Drive Storage (Forked and Updated)

Due to recent updates in Google's policies, OAuth has been deprecated and replaced by OAuth2 for authentication. To ensure continued functionality, we have forked and updated the original [laravel-google-drive-storage](https://github.com/yaza-putu/laravel-google-drive-storage) library, adding support for access tokens to enable seamless authentication with Google Drive.

## Changes in This Fork

1. **OAuth2 Integration**: The library now uses OAuth2 to comply with Google's updated authentication requirements.
2. **Access Token Management**: Added functionality to handle access tokens, including:
   - Retrieving tokens from cache or a file.
   - Refreshing expired tokens using the refresh token.
   - Caching and persisting the tokens for future use.
3. **Improved File Handling**: Ensured robust handling of paths and file operations for token storage.

## Setup Instructions

For detailed setup and configuration instructions, please refer to the original repository's README: [Original README](https://github.com/yaza-putu/laravel-google-drive-storage#readme).

## Why This Fork?

Google's policy changes made the original library incompatible with their new OAuth2 standards. This fork addresses these issues, ensuring the library remains functional and adheres to Google's latest requirements.

## Contributions

Feel free to report issues or contribute to this fork by submitting a pull request.
