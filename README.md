# IERG4210

Extensions:  
1. Change password (4%)
2. Reset password (8%)
3. AJAX when broswing categories and product.(4%)
4. AJAX in admin file upload. (3%)
4. All cookies are httpOnly and secure.(1%)

Peer hacking log:
1. by s7. Declared invalid by tutor.
2. by s12. Problem: broken auth in admin page.
           Root cause: missing exit() after header.
           Solution: Add exit() after header.
