# Page snapshot

```yaml
- main [ref=e4]:
  - generic [ref=e7]:
    - generic [ref=e8]:
      - link "Acceda a su cuenta" [ref=e9] [cursor=pointer]:
        - /url: /
        - img [ref=e11]
        - generic [ref=e13]: Acceda a su cuenta
      - generic [ref=e14]:
        - heading "Acceda a su cuenta" [level=1] [ref=e15]
        - paragraph [ref=e16]: Ingrese su correo y clave para acceder al sistema.
    - generic [ref=e17]:
      - generic [ref=e18]:
        - generic [ref=e19]:
          - generic [ref=e20]: Dirección de correo
          - textbox "Dirección de correo" [active] [ref=e21]:
            - /placeholder: email@example.com
        - generic [ref=e22]:
          - generic [ref=e23]:
            - generic [ref=e24]: Clave
            - link "Olvidó su clave?" [ref=e25] [cursor=pointer]:
              - /url: /forgot-password
          - textbox "Clave" [ref=e26]:
            - /placeholder: Clave de ingreso
        - generic [ref=e28]:
          - checkbox "Recordar" [ref=e29] [cursor=pointer]:
            - checkbox [ref=e30]
          - generic [ref=e31]: Recordar
        - button "Ingresar" [ref=e32] [cursor=pointer]
      - generic [ref=e33]:
        - text: No tiene una cuenta?
        - link "Registrar" [ref=e34] [cursor=pointer]:
          - /url: /register
```