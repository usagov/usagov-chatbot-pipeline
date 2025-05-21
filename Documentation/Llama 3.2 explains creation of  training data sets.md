# Llama 3.2 explains creation of  training data sets

You can use the `train_test_split` function from Scikit-learn library to split your dataset into training, validation and testing sets.

## Here's an example

```python
from sklearn.model_selection import train_test_split

# Assuming 'your_data' is a pandas DataFrame or list of lists representing your tokenized dataset
# 'target_variables' are the variables you want to use for splitting (in this case, we assume they're class labels)

train_data, val_test_data, train_labels, val_test_labels = train_test_split(your_data, target_variables, test_size=0.2, random_state=42)
val, test_data, val_labels, test_labels = train_test_split(val_test_data, val_test_labels, test_size=0.5, random_state=42)

# Now you have:

train_data  # training set
val_data    # validation set (used for tuning hyperparameters)
test_data   # testing set (for model evaluation)

train_labels  # training labels
val_labels    # validation labels
test_labels   # testing labels
```

## Explanation

In this example, `train_test_split` splits your data into three parts: a training set (`train_data` and `train_labels`), a validation set (`val_data`, `val_labels` used for tuning hyperparameters) and a testing set (`test_data` and `test_labels`). The `test_size=0.2` parameter means that 20% of your data will be used for validation and the remaining 80% will be used for training.

## Caveat

Please adjust the code according to your actual dataset structure and splitting strategy.
