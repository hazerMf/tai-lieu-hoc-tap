#include <openssl/des.h>
#include <sys/time.h> // For time measures
#include <string.h>
#include <stdio.h>
#include <stdlib.h>
#include <ctype.h>

// Encryption/Decryption switches
#define ENC 1
#define DEC 0

// From char to DES_LONG (be aware that c is shifted)
#define c2l(c,l)    (l =((DES_LONG)(*((c)++))), \
                     l|=((DES_LONG)(*((c)++)))<< 8L, \
                     l|=((DES_LONG)(*((c)++)))<<16L, \
                     l|=((DES_LONG)(*((c)++)))<<24L)

// From DES_LONG to char (be aware that c is shifted)
#define l2c(l,c)	(*((c)++)=(unsigned char)(((l)     )&0xff), \
                     *((c)++)=(unsigned char)(((l)>> 8L)&0xff), \
                     *((c)++)=(unsigned char)(((l)>>16L)&0xff), \
                     *((c)++)=(unsigned char)(((l)>>24L)&0xff))

void write_output(const char *filename, const unsigned char *in)
{
    // Now... Open the file binary with writing capabilities
    FILE *fp = fopen(filename, "wb");

    // If it can't be open, then return an error message
    if (fp == NULL) {fputs ("File error", stderr); exit (1);}

    // Write the in-array to specificed file-location
    fwrite(in, sizeof(unsigned char), strlen((const char *)in), fp);

    // Close the it
    fclose(fp);
}

const unsigned char *read_inputtext(const char *filename)
{
    // Total number of bytes
    unsigned long fsize;

    // Result of reading the file
    size_t result;

    // Now... Open the file binary with reading capabilities
    FILE *fp = fopen(filename, "rb");

    // If it can't be open, then return an error message
    if (fp == NULL) {fputs ("File error",stderr); exit (1);}

    /* Find out the number of bytes */
    fseek(fp, 0, SEEK_END);
    fsize = ftell(fp);      /* Get the size of the file */
    rewind(fp);             /* Go back to the start */

    // Allocate the buffer + 1 for termination
    unsigned char* buffer = malloc(fsize * sizeof *buffer + 1);

    // Test that everything went as we expected
    if(buffer == NULL) { fputs("Memory error!", stderr); exit(2); }

    // Read the buffer
    result = fread(buffer, 1, fsize * sizeof *buffer, fp);

    // Something went wrong when we read the file; sizes to not match
    if (result != fsize) {fputs ("Reading error", stderr); exit (3);}

    // Terminate the str
    buffer[fsize] = '\0';

    // Close the file
    fclose(fp);

    // Return the pointer to the dynamic allocated array
    return buffer;
}

void str2DES_cblock(const char *str, DES_cblock *out)
{
    // Make a char pointer and point it at the start of the array
    unsigned char *o;
    o = out[0];

    // Read the string
    int i;
    for (i = 0; i < 8; i++)
        sscanf(&(str[i*2]),"%2hhx", o++);
}

void my_des_cbc_encrypt(unsigned char *input, unsigned char *output, long length, DES_key_schedule ks, DES_cblock *ivec, int env){
  /*
    Arguments: 
      - unsigned char *input: a string (plaintext) for the input of the encryption
      - unsigned char *output: the output of the encryption (ciphertext)
      - long length: size of the input (in bytes)
      - DES_key_schedule ks: key for encryption (and decryption)
      - DES_cblock *ivec: initialization vector
      - int env: encryption/decryption switches (1 for Encryption, 0 for Decryption)

    Returns: None 

    Hints: 
    - Assume that the input length (in byte) is a multiple of 8
    - Try to undestand the macros l2c and c2l. They are important in implementation of CBC
  */
  
  unsigned char *iv;            // Initialization vector
  long l = length;              

  DES_LONG xor0, xor1;
  DES_LONG in0, in1;
  DES_LONG data[2];
  /* 
     Addtional variables (if needed)
  */

  iv = ivec[0];

  //Initialize XOR-variables
  c2l(iv, xor0); 
  c2l(iv, xor1);   
 
  //Handling 8 bytes of input data each time inside the for loop.
  for(l -=8; l >= 0; l -=8){
    /* 
      -------------------------------------------
      Your implementation of DES in CBC mode.
      Hint: Using DES_encrypt1() funtion of the openssl library
      -------------------------------------------
    */ 
    c2l(input, in0);
    c2l(input, in1);

    if (env == ENC) {
      in0 ^= xor0; in1 ^= xor1;
      data[0] = in0; data[1] = in1;
      DES_encrypt1(data, &ks, ENC);
      xor0 = data[0]; xor1 = data[1];
      l2c(data[0], output); l2c(data[1], output);
    } else {
      data[0] = in0; data[1] = in1;
      DES_encrypt1(data, &ks, DEC);
      data[0] ^= xor0; data[1] ^= xor1;
      xor0 = in0; xor1 = in1;
      l2c(data[0], output); l2c(data[1], output);
    }
  }
}

int main(int argc, char *argv[])
{
    int k;
    DES_key_schedule key;
    DES_cblock iv, cbc_key;


    /*
      Other variables
    */
    const unsigned char *input;
    unsigned char *output, *output_builtin;
    long length;

    /*
      Check number of command line arguments
    */
    if (argc != 5) {
        fprintf(stderr, "Usage: %s iv key inputfile outputfile\n", argv[0]);
        return 1;
    }

    /*
      Check key and initialization vector validities. (comprise of Hexadicimal digits or not)
      Hint: you might want to use the strcpy() function 
    */
    if (strlen(argv[1]) != 16 || strlen(argv[2]) != 16) {
        fprintf(stderr, "Error: IV and Key must be 16 hex characters each.\n");
        return 1;
    }
    for (int i = 0; i < 16; i++) {
        if (!isxdigit(argv[1][i]) || !isxdigit(argv[2][i])) {
            fprintf(stderr, "Error: IV and Key must contain only hex digits.\n");
            return 1;
        }
    }

    /*
      Convert key and initialization vector from string to DES_cblock
    */
    str2DES_cblock(argv[1], &iv);
    str2DES_cblock(argv[2], &cbc_key);

    if ((k = DES_set_key_checked(&cbc_key, &key)) != 0) {
        fprintf(stderr, "Key error: %d\n", k);
        return 1;
    }
    
    /* 
      Use the function read_inputtext() to get input from file (e..g., "test.des")
    */
    input = read_inputtext(argv[3]);
    length = strlen((const char *)input);

    if (length % 8 != 0) {
        fprintf(stderr, "Error: Input length must be multiple of 8 bytes.\n");
        free((void*)input);
        return 1;
    }

    output = malloc(length);
    output_builtin = malloc(length);
    
    
    /*
      Use the unction my_des_cbc_encrypt() for ecryption  
    */
    my_des_cbc_encrypt((unsigned char *)input, output, length, key, &iv, ENC);

    
    /*
      Use the function write_output() to write the ouput (the encrypted message) to a file
    */
    write_output(argv[4], output);


    /*
    Compare the result with that using built-in funtion DES_cbc_encrypt(). 
    Details of des_cbc_encrypt() can be seen at http://web.mit.edu/macdev/Development/MITKerberos/MITKerberosLib/DESLib/Documentation/api.html 
    */ 
    DES_cblock iv_copy;
    memcpy(iv_copy, iv, sizeof(DES_cblock));
    DES_cbc_encrypt(input, output_builtin, length, &key, &iv_copy, ENC);
   

    /*
      DES_cbc_encrypt();      
    */

    
    /*
     Print out ciphertexts from  my_des_cbc_encrypt() and DES_cbc_encrypt() to compare
    */
    printf("my_des_cbc_encrypt output:\n");
    for (int i = 0; i < length; i++) printf("%02X", output[i]);
    printf("\n");

    printf("DES_cbc_encrypt output:\n");
    for (int i = 0; i < length; i++) printf("%02X", output_builtin[i]);
    printf("\n");

    free((void*)input);
    free(output);
    free(output_builtin);
  
    return 0;
}
