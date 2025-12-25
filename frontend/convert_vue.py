#!/usr/bin/env python3
"""
Convert Vue Router components to Nuxt 3 components
"""
import re
import sys

def convert_vue_to_nuxt(content):
    """Convert Vue Router syntax to Nuxt syntax"""
    
    # Replace router-link with NuxtLink
    content = re.sub(r'<router-link', '<NuxtLink', content)
    content = re.sub(r'</router-link>', '</NuxtLink>', content)
    
    # Replace useRouter with useRouter from Nuxt
    content = re.sub(
        r"import { useRouter } from 'vue-router'",
        "// useRouter is auto-imported in Nuxt",
        content
    )
    
    # Replace store imports
    content = re.sub(
        r"import { useAuthStore } from ['\"](.*?)stores/auth['\"]",
        "// TODO: Implement auth composable or store",
        content
    )
    
    # Add process.client checks for browser-only code
    # This is a simple heuristic - you may need to adjust
    
    return content

def main():
    if len(sys.argv) < 3:
        print("Usage: python convert_vue.py <input_file> <output_file>")
        sys.exit(1)
    
    input_file = sys.argv[1]
    output_file = sys.argv[2]
    
    with open(input_file, 'r', encoding='utf-8') as f:
        content = f.read()
    
    converted = convert_vue_to_nuxt(content)
    
    with open(output_file, 'w', encoding='utf-8') as f:
        f.write(converted)
    
    print(f"Converted {input_file} -> {output_file}")

if __name__ == '__main__':
    main()
