# Troubleshooting Guide

## Common Issues and Solutions

### Token Not Working

**Problem**: API requests return 401 Unauthorized errors despite including a token.

**Solution**: 
- Ensure the token is included correctly in the Authorization header as `Bearer <token>`
- Check if the token has expired
- Verify the token hasn't been invalidated by a logout
- Confirm you're using the most recent token

### CORS Errors

**Problem**: Browser console shows CORS-related errors when making API requests.

**Solution**:
- Check `config/cors.php` configuration
- Verify your domain is listed in `SANCTUM_STATEFUL_DOMAINS` in `.env`
- Ensure the `SESSION_DOMAIN` in `.env` is properly set
- Check that cookies are being properly handled

### Database Errors

**Problem**: Database operations fail or return errors.

**Solution**:
- Verify `.env` database configuration
- Ensure migrations are up to date: `php artisan migrate:status`
- Check database permissions
- For SQLite, ensure the database file exists and is writable

### Authentication Issues

**Problem**: Unable to log in or register.

**Solution**:
- Clear browser cookies and try again
- Verify email is properly formatted
- Check password meets minimum requirements
- Ensure the user doesn't already exist when registering
- Check server logs for detailed error messages

### API Rate Limiting

**Problem**: Receiving too many requests error (429).

**Solution**:
- Check your request frequency
- Review rate limiting configuration in `RouteServiceProvider`
- Consider implementing caching for frequently accessed data
- Request rate limit increase if needed

## Getting Help

If you're still experiencing issues:

1. Check the Laravel log files in `storage/logs`
2. Review the API documentation
3. Run tests to verify system functionality
4. Contact the development team with:
   - Detailed error message
   - Steps to reproduce
   - Environment information
