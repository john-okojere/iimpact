# Serve plain HTML/CSS with Python
FROM python:3.12-alpine

# Make logs appear immediately
ENV PYTHONDONTWRITEBYTECODE=1 \
    PYTHONUNBUFFERED=1

WORKDIR /site
# copy your static site (index.html, /assets, /css, etc.)
COPY . /site

# Run http.server on the port Railway assigns; fallback to 8000 if not set
CMD ["sh", "-c", "PORT=${PORT:-8000}; echo \"Starting static server on :$PORT\"; python -m http.server \"$PORT\" --bind 0.0.0.0"]
