// Select the search input and add an event listener for the "keydown" event
const searchInput = document.getElementById('search-box');

searchInput.addEventListener('keydown', (event) => {
  if (event.key === 'Enter') {
    const query = searchInput.value.trim();
    if (query) {
      // Perform the search logic
      console.log(`Searching for: ${query}`);
      
      // Highlight matching text on the page
      highlightText(query);
    }
  }
});

// Function to highlight matching text
function highlightText(query) {
  // Remove previous highlights
  const previousHighlights = document.querySelectorAll('.highlight');
  previousHighlights.forEach((highlight) => {
    const parent = highlight.parentNode;
    parent.replaceChild(document.createTextNode(highlight.textContent), highlight);
  });

  // Search for matching text and highlight it
  const bodyText = document.body.innerHTML;
  const regex = new RegExp(`(${query})`, 'gi');
  const highlightedText = bodyText.replace(regex, '<span class="highlight">$1</span>');
  document.body.innerHTML = highlightedText;

  // Scroll to the first match
  const firstMatch = document.querySelector('.highlight');
  if (firstMatch) {
    firstMatch.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }
}

// Add CSS for the highlight class
const style = document.createElement('style');
style.textContent = `
  .highlight {
    background-color: yellow;
    color: black;
  }
`;
document.head.appendChild(style);