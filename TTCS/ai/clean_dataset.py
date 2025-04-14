import pandas as pd
import numpy as np

def clean_dataset():
    try:
        # Read the CSV file
        print("Reading dataset...")
        data = []
        with open('dataset/keypoint.csv', 'r') as f:
            for i, line in enumerate(f, 1):
                try:
                    # Split the line and convert to numbers
                    values = [float(x) for x in line.strip().split(',')]
                    # Check if the row has correct number of columns (1 label + 42 features)
                    if len(values) == 43:
                        data.append(values)
                except Exception as e:
                    print(f"Skipping invalid row {i}: {str(e)}")
        
        # Convert to DataFrame
        df = pd.DataFrame(data)
        
        # Save cleaned dataset
        print(f"Saving cleaned dataset with {len(df)} valid samples...")
        df.to_csv('dataset/keypoint_cleaned.csv', header=False, index=False)
        
        # Backup original file
        import shutil
        shutil.copy2('dataset/keypoint.csv', 'dataset/keypoint_backup.csv')
        
        # Replace original with cleaned version
        shutil.move('dataset/keypoint_cleaned.csv', 'dataset/keypoint.csv')
        
        print("Dataset cleaned successfully!")
        print(f"Original file backed up as 'keypoint_backup.csv'")
        
        # Print some statistics
        labels = df[0].astype(int)
        print("\nSamples per class:")
        for label in sorted(labels.unique()):
            count = (labels == label).sum()
            print(f"Class {label}: {count} samples")
            
    except Exception as e:
        print(f"Error cleaning dataset: {str(e)}")

if __name__ == "__main__":
    clean_dataset() 