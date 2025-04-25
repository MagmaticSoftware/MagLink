#!/bin/bash

CHANGELOG_FILE="CHANGELOG.md"

# Recupera ultimo tag
LAST_TAG=$(git describe --tags --abbrev=0 2>/dev/null)

if [ -z "$LAST_TAG" ]; then
  LAST_TAG="0.0.0"
fi

echo "🔍 Last release tag: $LAST_TAG"

# Ottieni log dei commit da ultimo tag
COMMITS=$(git log "$LAST_TAG"..HEAD --pretty=format:"%s")

echo ""
echo "📝 Commits since $LAST_TAG:"
echo "$COMMITS"
echo ""

# Funzione per suggerire tipo di bump
suggest_bump() {
  if echo "$COMMITS" | grep -i -q -E 'breaking|BREAKING CHANGE'; then
    echo "major"
  elif echo "$COMMITS" | grep -i -q -E '^feat|add|new'; then
    echo "minor"
  else
    echo "patch"
  fi
}

SUGGESTED=$(suggest_bump)

# Calcola nuova versione
IFS='.' read -r MAJOR MINOR PATCH <<< "${LAST_TAG#v}"

case "$SUGGESTED" in
  major)
    ((MAJOR++))
    MINOR=0
    PATCH=0
    ;;
  minor)
    ((MINOR++))
    PATCH=0
    ;;
  patch)
    ((PATCH++))
    ;;
esac

NEXT_VERSION="$MAJOR.$MINOR.$PATCH"
echo "📦 Suggested next version: v$NEXT_VERSION ($SUGGESTED)"

read -p "Enter release version [v$NEXT_VERSION]: " INPUT
VERSION=${INPUT:-v$NEXT_VERSION}

read -p "⚠️ Confirm release of $VERSION? [y/N]: " CONFIRM
if [[ "$CONFIRM" != "y" && "$CONFIRM" != "Y" ]]; then
  echo "❌ Release aborted."
  exit 1
fi

# ⬆️ Aggiorna il changelog
DATE=$(date +%Y-%m-%d)
HEADER="## [v$VERSION] - $DATE"

# Prepara changelog temporaneo
TEMP_FILE=$(mktemp)

echo "$HEADER" > "$TEMP_FILE"
echo "" >> "$TEMP_FILE"

# Raggruppa per tipo
if echo "$COMMITS" | grep -i -q -E '^feat|add|new'; then
  echo "### Added" >> "$TEMP_FILE"
  echo "$COMMITS" | grep -i -E '^feat|add|new' | sed 's/^/- /' >> "$TEMP_FILE"
  echo "" >> "$TEMP_FILE"
fi

if echo "$COMMITS" | grep -i -q -E '^fix|bug'; then
  echo "### Fixed" >> "$TEMP_FILE"
  echo "$COMMITS" | grep -i -E '^fix|bug' | sed 's/^/- /' >> "$TEMP_FILE"
  echo "" >> "$TEMP_FILE"
fi

if echo "$COMMITS" | grep -i -v -E '^feat|add|new|fix|bug'; then
  echo "### Changed" >> "$TEMP_FILE"
  echo "$COMMITS" | grep -i -v -E '^feat|add|new|fix|bug' | sed 's/^/- /' >> "$TEMP_FILE"
  echo "" >> "$TEMP_FILE"
fi

# Inserisci in cima al changelog
if [ -f "$CHANGELOG_FILE" ]; then
  cat "$TEMP_FILE" "$CHANGELOG_FILE" > "${CHANGELOG_FILE}.new"
  mv "${CHANGELOG_FILE}.new" "$CHANGELOG_FILE"
else
  mv "$TEMP_FILE" "$CHANGELOG_FILE"
fi

# Esegui commit, tag e push
git add .
git commit -m "Release v$VERSION"
git tag -a "v$VERSION" -m "Release v$VERSION"
git push origin main
git push origin "v$VERSION"

echo "✅ Released v$VERSION and updated CHANGELOG.md"
echo "🚀 Release complete!"