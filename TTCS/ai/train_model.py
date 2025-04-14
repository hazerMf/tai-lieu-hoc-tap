import torch
import torch.nn as nn
import numpy as np
import pandas as pd
from torch.utils.data import Dataset, DataLoader
import os

class HandSignDataset(Dataset):
    def __init__(self, csv_file):
        self.data = pd.read_csv(csv_file, header=None)
        
    def __len__(self):
        return len(self.data)
    
    def __getitem__(self, idx):
        row = self.data.iloc[idx]
        label = row[0]
        features = row[1:].values.astype(np.float32)
        # Normalize features
        features = (features - np.mean(features)) / np.std(features)
        return torch.tensor(features), torch.tensor(label, dtype=torch.long)

def load_or_create_model():
    # Define model architecture
    model = nn.Sequential(
        nn.Linear(42, 20),
        nn.ReLU(),
        nn.Linear(20, 10),
        nn.ReLU(),
        nn.Linear(10, 8)  # 8 classes without Softmax
    )
    
    # Try to load existing model
    if os.path.exists('best_model.pth'):
        try:
            print("Loading existing model...")
            state_dict = torch.load('best_model.pth')
            model.load_state_dict(state_dict)
            print("Existing model loaded successfully!")
        except Exception as e:
            print(f"Error loading existing model: {e}")
            print("Creating new model instead...")
    else:
        print("No existing model found. Creating new model...")
    
    return model

def train_model():
    # Check if dataset exists
    if not os.path.exists('dataset/keypoint.csv'):
        print("Error: No training data found. Please collect training data first.")
        return
    
    # Create dataset and dataloader
    dataset = HandSignDataset('dataset/keypoint.csv')
    dataloader = DataLoader(dataset, batch_size=32, shuffle=True)
    
    # Load existing model or create new one
    model = load_or_create_model()
    
    # Define loss function and optimizer
    criterion = nn.CrossEntropyLoss()
    optimizer = torch.optim.Adam(model.parameters(), lr=0.001)
    
    # Training loop
    num_epochs = 100
    best_accuracy = 0
    
    print("Starting training...")
    for epoch in range(num_epochs):
        model.train()
        total_loss = 0
        correct = 0
        total = 0
        
        for features, labels in dataloader:
            optimizer.zero_grad()
            outputs = model(features)
            loss = criterion(outputs, labels)
            loss.backward()
            optimizer.step()
            
            total_loss += loss.item()
            _, predicted = torch.max(outputs.data, 1)
            total += labels.size(0)
            correct += (predicted == labels).sum().item()
        
        accuracy = 100 * correct / total
        print(f'Epoch [{epoch+1}/{num_epochs}], Loss: {total_loss/len(dataloader):.4f}, Accuracy: {accuracy:.2f}%')
        
        # Save best model
        if accuracy > best_accuracy:
            best_accuracy = accuracy
            # Save with epoch number, accuracy and timestamp
            timestamp = pd.Timestamp.now().strftime('%Y%m%d_%H%M%S')
            model_name = f'best_model_epoch{epoch+1}_acc{accuracy:.2f}_{timestamp}.pth'
            
            # Remove previous best model from this session if it exists
            for file in os.listdir():
                if file.startswith('best_model_epoch') and file.endswith('.pth'):
                    os.remove(file)
            
            # Save new best model
            torch.save(model.state_dict(), model_name)
            # Also save as the main model file
            torch.save(model.state_dict(), 'best_model.pth')
            print(f'New best model saved as: {model_name}')
    
    print(f'Training completed. Best accuracy: {best_accuracy:.2f}%')

if __name__ == "__main__":
    train_model() 