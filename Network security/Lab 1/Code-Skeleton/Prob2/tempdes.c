#include <openssl/des.h>
#include <sys/time.h> // For time measures
#include <string.h>
#include <stdio.h>
#include <stdlib.h>
#include <ctype.h>

// Encryption/Decryption switches
#define ENC 1
#define DEC 0

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


int main(int argc, char *argv[])
{
    int k;
    DES_key_schedule key;
    DES_cblock iv, cbc_key;

    const char *str_iv  = "fedcba9876543210";                //Initialization vector, represeted as a string of hexadecimal digits
    const char *str_key = "40fedf386da13d57";                //Key, represeted as a string of hexadecimal digits

    /* 
       Addtional variables (if needed)
    */
   const unsigned char *input;
   unsigned char *output, *decrypted;
   long length;
   struct timeval t_start, t_end;
   long elapsed_enc, elapsed_dec;
    
    /* 
       Read text file and print out its length (in bytes)
       Hint: use the function read_inputtext()
    */

   if (argc != 2) {
      fprintf(stderr, "Usage: %s inputfile\n", argv[0]);
      return 1;
   }
   input = read_inputtext(argv[1]);
   length = strlen((const char *)input);
   printf("File size: %ld bytes\n", length);

   output = malloc(length);
   decrypted = malloc(length);

   str2DES_cblock(str_iv, &iv);
   str2DES_cblock(str_key, &cbc_key);

    /*
       DES encryption and its processing time
       Use the built-in function DES_cbc_encrypt() with ENC mode for encryption
       Hint: you might want to use the gettimeofday() function to measure running time
    */
   DES_cblock iv_enc;
   memcpy(iv_enc, iv, sizeof(DES_cblock));
   gettimeofday(&t_start, 0);
   DES_cbc_encrypt(input, output, length, &key, &iv_enc, ENC);
   gettimeofday(&t_end, 0);
   elapsed_enc = (t_end.tv_sec - t_start.tv_sec) * 1000000L +
               (t_end.tv_usec - t_start.tv_usec);
   printf("Encryption time: %ld us\n", elapsed_enc);

    /*
       DES decryption and its processing time
       Use the built-in function DES_cbc_encrypt() with DEC mode for decryption
       Hint: you might want to use the gettimeofday() function to measure running time
    */
   DES_cblock iv_dec;
   memcpy(iv_dec, iv, sizeof(DES_cblock));
   gettimeofday(&t_start, 0);
   DES_cbc_encrypt(output, decrypted, length, &key, &iv_dec, DEC);
   gettimeofday(&t_end, 0);
   elapsed_dec = (t_end.tv_sec - t_start.tv_sec) * 1000000L +
                  (t_end.tv_usec - t_start.tv_usec);
   printf("Decryption time: %ld us\n", elapsed_dec);
      
   free((void*)input);
   free(output);
   free(decrypted);

    return 0;
}
