# super tiny image with Python
FROM python:3.12-alpine

# copy your static site (index.html, /assets, /css, etc.)
WORKDIR /site
COPY . /site

# Railway sets $PORT at runtime. Bind to 0.0.0.0 so the proxy can reach it.
CMD ["sh", "-c", "python -m http.server $PORT --bind 0.0.0.0"]
